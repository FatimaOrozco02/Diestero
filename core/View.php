<?php

declare(strict_types=1);

namespace Core;

use RuntimeException;

final class View
{
   private string $viewsPath;
   private string $publicPath;

   private array $libStyles = [];
   private array $styles = [];
   private array $libScripts = [];
   private array $scripts = [];

   public function __construct(?string $viewsPath = null, ?string $publicPath = null)
   {
      $this->viewsPath = $viewsPath ?? dirname(__DIR__) . '/resources/views/';
      $this->publicPath = $publicPath ?? dirname(__DIR__) . '/public';
   }

   /* =============================
       Auto Assets
    ============================= */

   public function registerAutoAssets(string $viewFile): void
   {
      $cssFile = $viewFile . '.css';
      if (file_exists($this->publicPath . '/css/' . $cssFile)) {
         $this->addStyle($cssFile);
      }
   
      $jsFile = $viewFile . '.js';
      if (file_exists($this->publicPath . '/js/' . $jsFile)) {
         $this->addScript($jsFile);
      }
   }

   /* =============================
       Assets Management
    ============================= */

   public function addLibStyles(string $href): self
   {
      $href = trim($href);      
      if ($href !== '') {
         $this->libStyles[] = 'lib/' . $href;
         $this->libStyles = array_values(array_unique($this->libStyles));
      }

      return $this;
   }

   public function addStyle(string $href): self
   {
      $href = trim($href);
      if ($href !== '') {
         $this->styles[] = 'css/' . $href;
         $this->styles = array_values(array_unique($this->styles));
      }

      return $this;
   }

   public function addLibScript(string $href): self
   {
      Log::createLog()->info("Adding lib script: $href");
      $href = trim($href);
      if ($href !== '') {
         $this->libScripts[] = 'lib/' . $href;
         $this->libScripts = array_values(array_unique($this->libScripts));
      }

      return $this;
   }

   public function addScript(string $href): self
   {
      $href = trim($href);
      if ($href !== '') {
         $this->scripts[] = 'js/' . $href;
         $this->scripts = array_values(array_unique($this->scripts));
      }

      return $this;
   }

   /* =============================
       Render
    ============================= */

   public function render(string $viewFile, array $data = [], ?string $layout = 'layouts/main'): string {        
      $viewPath = $this->viewsPath . ltrim($viewFile, '/');
      
      if (!str_ends_with($viewPath, '.php')) {
            $viewPath .= '.php';
      }

      if (!file_exists($viewPath)) {
            throw new RuntimeException('View not found: ' . $viewFile);
      }

      // Cargar helpers solo al renderizar HTML
      require_once dirname(__DIR__) . '/resources/helpers.php';

      // Variables default disponibles en el layout
      $baseUrl = Config::get('app.url');
      $title = Config::get('app.name');
      $sessionUser = Session::get('user');
      $sessionUserId = ($sessionUser && $sessionUser['id']) ?? $sessionUser['id'];
      $sessionUserProfileId = ($sessionUser && $sessionUser['profile_id']) ?? $sessionUser['profile_id'];
      $sessionLayoutAside = Session::get("layout_aside") ?? false;

      $libStyles = $this->libStyles;
      $styles = $this->styles;
      $libScripts = $this->libScripts;      
      $scripts = $this->scripts;
      $sessionCsrfToken = Session::has('_csrf_token') ? Session::get('_csrf_token') : (Session::set('_csrf_token', bin2hex(random_bytes(32))) ?? '');

      extract($data, EXTR_SKIP);
      
      ob_start();
      require $viewPath;
      $content = ob_get_clean();

      if ($layout === null) {
            return $content;
      }

      $layoutPath = __DIR__ . "/../resources/" . ltrim($layout, '/');

      if (!str_ends_with($layoutPath, '.php')) {
            $layoutPath .= '.php';
      }

      if (!file_exists($layoutPath)) {
            throw new RuntimeException('Layout not found: ' . $layout);
      }

      ob_start();
      require $layoutPath;

      return ob_get_clean();
   }
}
