<?php

declare(strict_types = 1);

namespace Core;

use RuntimeException;

final class Router {
    private Request $request;
    private Response $response;
    private array $routes = [];
    private array $groupMiddlewares = [];
    private string $groupPrefix = "";

    public function __construct(Request $request, Response $response) {
        $this->request = $request;
        $this->response = $response;
    }

    public function group(array $options, callable $callback): void {
        $prevPrefix = $this->groupPrefix;
        $prevMids = $this->groupMiddlewares;

        $this->groupPrefix = $prevPrefix . ($options["prefix"] ?? "");

        if (!empty($options["middlewares"]) && is_array($options["middlewares"])) {
            $this->groupMiddlewares = array_merge($prevMids, $options["middlewares"]);
        }

        $callback($this);

        $this->groupPrefix = $prevPrefix;
        $this->groupMiddlewares = $prevMids;
    }

    private function normalize(string $path): string {
        $path = '/' . ltrim($path, '/');
        $path = preg_replace('#/+#', '/', $path) ?: '/';
        $path = rtrim($path, '/');
        return $path === '' ? '/' : $path;
    }

    private function compilePattern(string $pattern): array {
        // /users/{id} => regex con named capture
        $params = [];

        $regex = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', function ($m) use (&$params) {
            $params[] = $m[1];
            return '(?P<' . $m[1] . '>[a-zA-Z0-9\-_]+)';
        }, $pattern);

        $regex = '#^' . $regex . '$#';

        return [$regex, $params];
    }

    private function parseMiddleware(string $middleware): array {
        // Ej: RoleMiddleware:1,2,3
        if (!str_contains($middleware, ':')) {
            return [$middleware, []];
        }

        [$class, $paramString] = explode(':', $middleware, 2);
        $params = array_map('trim', explode(',', $paramString));

        return [$class, $params];
    }

    private function buildMiddlewarePipeline(array $middlewares, callable $finalHandler): callable {
        $next = $finalHandler;

        for ($i = count($middlewares) - 1; $i >= 0; $i--) {
            $middlewareClass = $middlewares[$i];

            $next = function (Request $request, Response $response) use ($middlewareClass, $next): void {
                [$class, $params] = $this->parseMiddleware($middlewareClass);

                $middleware = new $class(...$params);
                if (!$middleware instanceof Middleware) {
                $response->text('Invalid middleware: ' . $middlewareClass, 500);
                return;
                }
                $middleware->handle($request, $response, $next);
            };
        }

        return $next;
    }

   private function resolveHandler(mixed $handler, array $routeParams): callable {
        // Closure
        if (is_callable($handler)) {
            return function (Request $request, Response $response) use ($handler, $routeParams): void {
                $handler($request, $response, $routeParams);
            };
        }

        // "Controller@method"
        if (is_string($handler) && str_contains($handler, '@')) {
            [$controller, $method] = explode('@', $handler, 2);

            $controllerClass = "App\\Controllers\\$controller";
            
            if (!class_exists($controllerClass)) {
                $systemFolder = Session::get("system_accessed") ?? "crm_gdc";
                
                $controllerClass = "App\\Controllers\\$systemFolder\\$controller";                
            }

            return function (Request $request, Response $response) use ($controllerClass, $method, $routeParams): void {
                if (!class_exists($controllerClass)) {
                $response->text('Controller not found: ' . $controllerClass, 500);
                return;
                }

                $instance = new $controllerClass($request, $response);

                if (!method_exists($instance, $method)) {
                    $response->text('Method not found: ' . $controllerClass . '::' . $method, 500);
                    return;
                }

                try {
                    $reflectionMethod = new \ReflectionMethod($instance, $method);
                    $args = $this->resolveMethodArguments($reflectionMethod, $request, $response, $routeParams);

                    $reflectionMethod->invokeArgs($instance, $args);
                } catch (\Throwable $e) {
                    $response->text('Handler error: ' . $e->getMessage(), 500);
                }
            };
        }

        return function (Request $request, Response $response): void {
            $response->text('Invalid route handler', 500);
        };
    }

    public function addRoute(string $method, string $path, mixed $handler, array $middlewares): void {
        $method = strtoupper($method);
        $pattern = $this->normalize($this->groupPrefix . $path);

        [$regex, $params] = $this->compilePattern($pattern);

        $allMiddlewares = array_merge($this->groupMiddlewares, $middlewares);

        $this->routes[] = [
            "method" => $method,
            "pattern" => $pattern,
            "regex" => $regex,
            "params" => $params,
            "handler" => $handler,
            "middlewares" => $allMiddlewares
        ];
    }

    public function dispatch(): void {
        $method = $this->request->method();
        $path = $this->normalize($this->request->path());

        foreach ($this->routes as $route) {
            if ($route["method"] !== $method) {
                continue;
            }

            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }

            $routeParams = [];
            foreach ($route['params'] as $name) {
                if (isset($matches[$name])) {
                $routeParams[$name] = $matches[$name];
                }
            }

            $handler = $this->resolveHandler($route['handler'], $routeParams);

            $pipeline = $this->buildMiddlewarePipeline($route['middlewares'], $handler);
            $pipeline($this->request, $this->response);
            return;
        }

        $this->response->text('Not Found', 404);        
    }

    public function get(string $path, mixed $handler, array $middlewares = []): void {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, mixed $handler, array $middlewares = []): void {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    public function put(string $path, mixed $handler, array $middlewares = []): void {
        $this->addRoute('PUT', $path, $handler, $middlewares);
    }

    public function delete(string $path, mixed $handler, array $middlewares = []): void {
        $this->addRoute('DELETE', $path, $handler, $middlewares);
    }

    private function resolveMethodArguments(
      \ReflectionMethod $method,
      Request $request,
      Response $response,
      array $routeParams
   ): array {
      $args = [];
      $routeValues = array_values($routeParams);
      $routeIndex = 0;

      foreach ($method->getParameters() as $parameter) {
         $type = $parameter->getType();

         // Inyección por clase
         if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
            $typeName = $type->getName();

            if ($typeName === Request::class) {
               $args[] = $request;
               continue;
            }

            if ($typeName === Response::class) {
               $args[] = $response;
               continue;
            }
         }

         // Parámetros escalares desde la ruta
         if (array_key_exists($routeIndex, $routeValues)) {
            $args[] = $this->castRouteValue($routeValues[$routeIndex], $parameter);
            $routeIndex++;
            continue;
         }

         // Valor por defecto
         if ($parameter->isDefaultValueAvailable()) {
            $args[] = $parameter->getDefaultValue();
            continue;
         }

         // Nullable
         if ($type instanceof \ReflectionNamedType && $type->allowsNull()) {
            $args[] = null;
            continue;
         }

         throw new RuntimeException(
            'Unable to resolve parameter $' . $parameter->getName() . ' in ' . $method->getName()
         );
      }

      return $args;
   }

    private function castRouteValue(string $value, \ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        if (!$type instanceof \ReflectionNamedType) {
            return $value;
        }

        $typeName = $type->getName();

        return match ($typeName) {
            'int' => $this->filterInt($value, $parameter->getName()),
            'float' => $this->filterFloat($value, $parameter->getName()),
            'bool' => $this->filterBool($value, $parameter->getName()),
            'string' => $value,
            default => $value,
        };
    }

    private function filterInt(string $value, string $parameterName): int
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new RuntimeException('Route parameter "' . $parameterName . '" must be a valid integer.');
        }

        return (int) $value;
    }

    private function filterFloat(string $value, string $parameterName): float
    {
        if (filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
            throw new RuntimeException('Route parameter "' . $parameterName . '" must be a valid float.');
        }

        return (float) $value;
    }

    private function filterBool(string $value, string $parameterName): bool
    {
        $result = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($result === null) {
            throw new RuntimeException('Route parameter "' . $parameterName . '" must be a valid boolean.');
        }

        return $result;
    }
}