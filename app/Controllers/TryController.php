<?php 

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;
use Core\PasswordHasher;

final class TryController extends Controller
{
      public function password(): void
      {
            $hasher = new PasswordHasher();
            $password = 'gdc';
            $hash = $hasher->hash($password);
            $verify = $hasher->verify($password, $hash);

            echo "<pre>";
            echo "Password: $password\n";
            echo "Hash: $hash\n";
            echo "Verify: " . ($verify ? 'true' : 'false') . "\n";
            echo "</pre>";
      }
}