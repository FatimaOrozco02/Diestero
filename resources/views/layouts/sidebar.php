<!-- Navbar con hamburguesa -->
<nav class="navbar navbar-dark bg-dark d-lg-none">
   <div class="container-fluid">
      <a class="navbar-brand" href="#">Menú</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
         <span class="navbar-toggler-icon"></span>
      </button>
   </div>
</nav>

<!-- Sidebar -->
<aside id="sidebarMenu" class="col-12 col-lg-2 d-lg-block bg-gray paddingx-0 collapse">
   <div class="flex-shrink-0 py-4">
      <img src="<?= asset('img/sidebar.png') ?>" alt="Logo" class="img-fluid mb-4 px-3 mh-5r">

      <ul class="list-unstyled ps-0 py-2">
         <!--home-->
         <li class="mb-1 border-bottom border-top py-1" id="parent">
            <a href="<?= $baseUrl ?>" class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed text-white fs-4 fw-semibold text-wrap">
               Inicio
            </a>
         </li>

         <!--Registro denuncia-->
         <li class="mb-1 border-bottom py-1" id="parent">
            <button class="btn d-inline-flex align-items-center rounded border-0 collapsed text-white fs-4 fw-semibold dropdown-toggle text-wrap" data-bs-toggle="collapse" data-bs-target="#registerCollapse" aria-expanded="false">
               Registro de denuncia
            </button>
            <div class="collapse bg-gray-2" id="registerCollapse">
               <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                  <li class="bg-alt-main" id="complaint"><a href="<?= $baseUrl ?>quejas/nueva" class="link-body-emphasis d-inline-flex text-decoration-none rounded text-white fs-5 py-2 px-3">Denuncia</a></li>
                  <li class="bg-alt-main" id="advisory"><a href="<?= $baseUrl ?>asesorias/nueva" class="link-body-emphasis d-inline-flex text-decoration-none rounded text-white fs-5 py-2 px-3">Asesoría</a></li>
               </ul>
            </div>
         </li>
         <?php if ($sessionUser['profile_id'] != 5): ?>
            <!--Comunidad-->
            <?php if (strpos($_SERVER['REQUEST_URI'], 'comunidad/') !== false || strpos($_SERVER['REQUEST_URI'], 'usuarios/') !== false || strpos($_SERVER['REQUEST_URI'], 'defensores/') !== false): ?>
               <li class="mb-1 border-bottom py-1 bg-main-5" id="parent">
               <?php else: ?>
               <li class="mb-1 border-bottom py-1" id="parent">
               <?php endif; ?>
               <button class="btn d-inline-flex align-items-center rounded border-0 collapsed text-white fs-4 fw-semibold dropdown-toggle text-wrap" data-bs-toggle="collapse" data-bs-target="#communityCollapse" aria-expanded="false">
                  Comunidad
               </button>
               <div class="collapse bg-gray-2" id="communityCollapse">
                  <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                     <li class="bg-alt-main" id="users"><a href="<?= $baseUrl ?>usuarios" class="link-body-emphasis d-inline-flex text-decoration-none rounded text-white fs-5 py-2 px-3">Usuarios</a></li>
                     <li class="bg-alt-main" id="defenders"><a href="<?= $baseUrl ?>defensores" class="link-body-emphasis d-inline-flex text-decoration-none rounded text-white fs-5 py-2 px-3">Defensores</a></li>
                  </ul>
               </div>
               </li>
            <?php endif; ?>

            <!--Expedientes-->
            <?php if (strpos($_SERVER['REQUEST_URI'], 'expedientes/') !== false || strpos($_SERVER['REQUEST_URI'], 'expedientes?') !== false): ?>
               <li class="mb-1 border-bottom py-1 bg-main-5" id="parent">
               <?php else: ?>
               <li class="mb-1 border-bottom py-1" id="parent">
               <?php endif; ?>
               <a href="<?= $baseUrl ?>expedientes" class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed text-white fs-4 fw-semibold text-wrap">
                  Expedientes
               </a>
               </li>

               <?php if ($sessionUser['profile_id'] != 5): ?>
                  <!--Reportes-->
                  <li class="mb-1 border-bottom py-1" id="parent">
                     <a href="#" class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed text-white fs-4 fw-semibold text-wrap">
                        Reportes
                     </a>
                  </li>

                  <!--Analíticas-->
                  <li class="mb-1 border-bottom py-1" id="parent">
                     <a href="#" class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed text-white fs-4 fw-semibold text-wrap">
                        Analíticas
                     </a>
                  </li>

                  <!--Notificaciones-->
                  <li class="mb-1 border-bottom py-1" id="parent">
                     <a href="#" class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed text-white fs-4 fw-semibold text-wrap">
                        Notificaciones
                     </a>
                  </li>

                  <!--Agenda-->
                  <li class="mb-1 border-bottom py-1" id="parent">
                     <a href="#" class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed text-white fs-4 fw-semibold text-wrap">
                        Agenda
                     </a>
                  </li>

                  <!--Encuestas-->
                  <li class="mb-1 border-bottom py-1" id="parent">
                     <a href="#" class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed text-white fs-4 fw-semibold text-wrap">
                        Encuestas
                     </a>
                  </li>
               <?php endif; ?>
               <!--Configuraciones-->
               <?php if (strpos($_SERVER['REQUEST_URI'], 'configuracion/') !== false) { ?>
                  <li class="mb-1 border-bottom py-1 bg-main-5" id="parent">
                  <?php } else { ?>
                  <li class="mb-1 border-bottom py-1" id="parent">
                  <?php } ?>

                  <a href="<?= $baseUrl ?>configuracion" class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed text-white fs-4 fw-semibold text-wrap">
                     Configuraciones
                  </a>
                  </li>
      </ul>
   </div>
</aside>