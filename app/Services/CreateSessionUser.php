<?php

declare(strict_types=1);

namespace App\Services;

use Core\Log;
use Core\Session;
use Exception;

final class CreateSessionUser {
    /* 
     * Crea la sesión del usuario después de un inicio de sesión exitoso
     * @param array $user Información del usuario (id, full_name, email, profile_id, clave_outlook, token_outlook, token)
     * @param bool $showLayoutAside Indica si se debe mostrar el layout aside (sidebar)
     * @return void
     */
    public static function createSession(array $user, bool $showLayoutAside = true): void {
        try {
            Session::set("user", [
                "id" => $user["id"],
                "full_name" => $user["full_name"],
                "email" => $user["email"],
                "profile_id" => $user["profile_id"],
                "clave_outlook" => $user["clave_outlook"] ?? "",
                "token_outlook" => $user["token_outlook"] ?? "",
                "token" => $user["token"] ?? "",
                "photo" => $user["photo"] ?? ""
            ]);

            Session::set("layout_aside", $showLayoutAside);

            //Log::createLog()->info("Sesión creada para el usuario: " . $user["email"]);
        } catch (Exception $e) {
            Log::createLog()->error("Error al crear la sesión del usuario: " . $e->getMessage());
            throw $e;
        }
    }
}