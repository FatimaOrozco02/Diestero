<?php

/** @var string $baseUrl */
/** @var array|null $sessionUser */
?>

<nav class="navbar navbar-expand-lg  bg-white py-3">
      <div class="container-fluid d-flex align-items-center justify-content-between px-5">
            <div class="container-fluid">

                  <img src="<?php echo $baseUrl ?>img/logo diestro_1200px_color.png" alt="Logo" width="70" height="64" class="d-inline-block align-text-top">

            </div>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                  <div class="navbar-nav d-flex align-items-center gap-5">
                        <a class="nav-link active text-uppercase fw-bold text-decoration-none" aria-current="page" href="<?php echo $baseUrl ?>somos">SOMOS</a>
                        <a class="nav-link text-uppercase fw-bold text-decoration-none" href="<?php echo $baseUrl ?>certificaciones">CERTIFICACIONES</a>
                        <a class="nav-link text-uppercase fw-bold text-decoration-none" href="<?php echo $baseUrl ?>soluciones_estrategicas">SOLUCIONES ESTRATÉGICAS</a>
                        <a class="nav-link text-uppercase fw-bold text-decoration-none" href="<?php echo $baseUrl ?>cfoaas">CFOAAS</a>
                        <a class="nav-link text-uppercase fw-bold text-decoration-none" href="<?php echo $baseUrl ?>socios">SOCIOS</a>
                        <a class="nav-link text-uppercase fw-bold text-decoration-none" href="<?php echo $baseUrl ?>contacto">CONTACTO</a>

                        <?php if (!empty($sessionUser)): ?>
                              <a class="nav-link text-uppercase fw-bold text-decoration-none" href="<?php echo $baseUrl ?>admin/certificaciones">Administrador</a>
                              <a class="nav-link text-uppercase fw-bold text-decoration-none" href="<?php echo $baseUrl ?>admin/cerrar_sesion">Cerrar sesión</a>
                        <?php endif; ?>

                  </div>
            </div>
      </div>
</nav>