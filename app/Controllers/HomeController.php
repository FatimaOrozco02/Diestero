<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;

final class HomeController  extends Controller
{
      public function index()
      {
            $this->response->json([
                  "message" => "Hola mundo"
            ]);
      }


      public function  home(): void
      {
            $this->render('home');
      }

      public function  certifications(): void
      {
            $this->view()->addLibScript('jquery-validation/jquery.validate.min.js');
            $this->view()->addScript('admin/scripts.js');
            $this->render('certifications/certifications');
      }

      public function  solutions(): void
      {
            $this->render('solutions/solutions');
      }

      public function  cfoaas(): void
      {
            $this->render('cfoaas/cfoaas');
      }

      public function  partners(): void
      {
            $this->render('partners/partners');
      }

      public function  contact(): void
      {
            $this->render('contact/contact');
      }
}
