<!-- <nav class="navbar navbar-expand-lg bg-white py-3 shadow-sm">
      <div class="container-fluid d-flex align-items-center justify-content-between px-5">
            <div class="container-fluid">

                  <img src="<?php echo $baseUrl ?>/img/logo diestro_1200px_color.png" alt="Logo" width="70" height="64" class="d-inline-block align-text-top">

            </div>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNavAltMarkup">
                  <div class="navbar-nav d-flex align-items-center gap-5">
                        <a class="nav-link active text-uppercase fw-bold text-decoration-none nav-custom-link" aria-current="page" href="<?php echo $baseUrl ?>">SOMOS</a>
                        <a class="nav-link text-uppercase fw-bold text-decoration-none nav-custom-link" href="<?php echo $baseUrl ?>certificaciones">CERTIFICACIONES</a>
                        <a class="nav-link text-uppercase fw-bold text-decoration-none nav-custom-link text-nowrap" href="<?php echo $baseUrl ?>soluciones_estrategicas">SOLUCIONES ESTRATÉGICAS</a>
                        <a class="nav-link text-uppercase fw-bold text-decoration-none nav-custom-link" href="<?php echo $baseUrl ?>cfoaas">CFOAAS</a>
                        <a class="nav-link text-uppercase fw-bold text-decoration-none nav-custom-link" href="<?php echo $baseUrl ?>socios">SOCIOS</a>
                        <a class="nav-link text-uppercase fw-bold text-decoration-none nav-custom-link" href="<?php echo $baseUrl ?>contacto">CONTACTO</a>

                        <?php if (!empty($sessionUser)): ?>
                              <a class="nav-link text-uppercase fw-bold text-decoration-none" href="<?php echo $baseUrl ?>admin/certificaciones">Administrador</a>
                              <a class="nav-link text-uppercase fw-bold text-decoration-none" href="<?php echo $baseUrl ?>admin/cerrar_sesion">Cerrar sesión</a>
                        <?php endif; ?>

                  </div>
            </div>
      </div>
</nav> -->

<nav class="navbar navbar-expand-lg bg-white py-3 shadow-sm">
      <div class="container-fluid d-flex align-items-center justify-content-between px-3 px-md-5" style="max-width: 1440px; margin: 0 auto;">
            
            <!-- CONTENEDOR LOGO -->
            <a class="navbar-brand d-flex align-items-center" href="<?php echo $baseUrl ?>">
                  <img src="<?php echo $baseUrl ?>img/logo diestro_1200px_color.png" alt="Logo" width="70" height="64" class="d-inline-block align-text-top">
            </a>

            <!-- BOTÓN MENÚ DE HAMBURGUESA (Solo visible en móviles) -->
            <button class="navbar-toggler border-0 menu-hamburguesa" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
            </button>

            <!-- ENLACES DE NAVEGACIÓN -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
                  <div class="navbar-nav d-flex flex-column flex-lg-row align-items-center gap-4 gap-lg-5 pt-3 pt-lg-0">
                        <a class="nav-link text-uppercase fw-bold text-decoration-none nav-custom-link" aria-current="page" href="<?php echo $baseUrl ?>">SOMOS</a>
                        <a class="nav-link text-uppercase fw-bold text-decoration-none nav-custom-link" href="<?php echo $baseUrl ?>certificaciones">CERTIFICACIONES</a>
                        <a class="nav-link text-uppercase fw-bold text-decoration-none nav-custom-link text-nowrap" href="<?php echo $baseUrl ?>soluciones_estrategicas">SOLUCIONES ESTRATÉGICAS</a>
                        <a class="nav-link text-uppercase fw-bold text-decoration-none nav-custom-link" href="<?php echo $baseUrl ?>cfoaas">CFOAAS</a>
                        <a class="nav-link text-uppercase fw-bold text-decoration-none nav-custom-link" href="<?php echo $baseUrl ?>socios">SOCIOS</a>
                        <a class="nav-link text-uppercase fw-bold text-decoration-none nav-custom-link" href="<?php echo $baseUrl ?>contacto">CONTACTO</a>

                        <?php if (!empty($sessionUser)): ?>
                              <a class="nav-link text-uppercase fw-bold text-decoration-none text-danger" href="<?php echo $baseUrl ?>admin/certificaciones">Administrador</a>
                              <a class="nav-link text-uppercase fw-bold text-decoration-none text-muted" href="<?php echo $baseUrl ?>admin/cerrar_sesion">Cerrar sesión</a>
                        <?php endif; ?>
                  </div>
            </div>

      </div>
</nav>
