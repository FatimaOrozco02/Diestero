<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\CreateSessionUser;
use Core\Controller;
use Core\Log;
use Core\Session;
use App\Models\crm_gdc\serviseOutlock;
use Exception;

final class AuthController extends Controller
{
   public function loginSimple(): void
   {
      try {
         $params = $this->request->body();

         if (!empty($_GET["code"]) && $_GET["code"] != "") {
            $token_outlook = $_GET["code"];
         } else if (empty($params["email"]) || empty($params["password"])) {
            $this->response->redirect("/", 401);
         }

            CreateSessionUser::createSession([
                "id" => 3,
                "full_name" => "Usuario de Prueba",
                "email" => $params["email"],
                "profile_id" => 1,
                "token_outlook" => '',
                "token" => ""
            ]);
            Session::set("system_accessed", "crm_gdc");
            // Session::set("system_accessed", "finanzas");
            // Session::set("system_accessed", "licitaciones");

         $this->response->json([
            "authenticated" => true
         ]);
      } catch (Exception $e) {
         Log::createLog()->error("Error in loginSimple, line: {$e->getLine()}");
         Log::createLog()->error($e->getMessage());
         $this->response->redirect("/", 500);
      }
   }

   /*public function loginOutlook(): void {
        $this->response->redirect("/CRM_GDC");
    }*/

    public function loginOutlook() {
        //Session::remove("user");
        $this->response->redirect("https://login.microsoftonline.com/0a2e7e32-c8cb-4f9c-83a6-8694cc64bb92/oauth2/v2.0/authorize?client_id=977a9a2b-6f10-4a14-8a45-10813d7b601f&response_type=code&redirect_uri=https://057b-200-55-49-2.ngrok-free.app/GDC/login/get_token_outlook&response_mode=query&scope=User.Read.All");
        //$this->response->redirect("https://login.microsoftonline.com/0a2e7e32-c8cb-4f9c-83a6-8694cc64bb92/oauth2/v2.0/authorize?client_id=977a9a2b-6f10-4a14-8a45-10813d7b601f&response_type=code&redirect_uri=https://ivette-nonseclusive-renato.ngrok-free.dev/CRM_GDC/inicio&response_mode=query&scope=User.Read.All");
    }

    public function tokenOutlook() {
        $token_outlook="";
        $user=array();
        if (!empty($_GET["code"]) && $_GET["code"] != "") {
            $token_outlook= $_GET["code"];

            $tokenModel = new serviseOutlock();
            $token = $tokenModel->token($token_outlook);
            if ("err"==$token) {
                $this->response->redirect("https://login.microsoftonline.com/0a2e7e32-c8cb-4f9c-83a6-8694cc64bb92/oauth2/v2.0/authorize?client_id=977a9a2b-6f10-4a14-8a45-10813d7b601f&response_type=code&redirect_uri=https://057b-200-55-49-2.ngrok-free.app/GDC/login/get_token_outlook&response_mode=query&scope=User.Read.All");
                exit();
            }else{
                $token_outlook= $_GET["code"];
    
                $user = $tokenModel->getUser($token_outlook, $token);
                $user["photo"] = $tokenModel->photouser($token);
            }
        }else if($_SESSION["user"]["token_outlook"]!=''){
            $token_outlook= $_SESSION["user"]["token_outlook"];
            $token= $_SESSION["user"]["token"];
            $tokenModel = new serviseOutlock();
            $photo = $tokenModel->getUser($token_outlook, $token);

            $user["photo"] = $tokenModel->photouser($token);
            
        }

        CreateSessionUser::createSession([
            "id" => $user["id"],
            "full_name" => $user["nombre"],
            "email" => $user["mail"],
            "profile_id" => 1,
            "token_outlook" => $token_outlook,
            "token" => $token,
            "photo" => $user["photo"]
        ]);
        
        Session::set("system_accessed", "crm_gdc");
         // Session::set("system_accessed", "finanzas");
         // Session::set("system_accessed", "licitaciones");
        $this->response->redirect("/GDC/inicio");
    }

   public function logout(): void
   {
      Session::remove("user");
      Session::remove("layout_aside");
      $this->response->redirect("/GDC");
   }
}
