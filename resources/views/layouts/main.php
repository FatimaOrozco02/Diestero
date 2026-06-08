<!DOCTYPE html>
<html lang="<?= e(app_locale()) ?>">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?= $title ?? 'Mi App' ?></title>

   <!-- Favicon -->
   <link rel="icon" type="image/x-icon" href="<?= asset('img/favicon.ico') ?>">

   <!-- Bootstrap 5 CSS -->
   <link rel="stylesheet" href="<?= asset('lib/bootstrap/bootstrap.min.css') ?>">
   <link rel="stylesheet" href="<?= asset('lib/bootstrap/bootstrap_extension.css') ?>">
   <!-- Font Awesome 6 -->
   <link rel="stylesheet" href="<?= asset('lib/fontawesome/css/fontawesome.min.css') ?>">
   <link rel="stylesheet" href="<?= asset('lib/fontawesome/css/all.min.css') ?>">
   <!-- Sweet Alert 2 CSS-->
   <link rel="stylesheet" href="<?= asset('lib/sweetalert/sweetalert2.min.css') ?>">

   <!-- Library CSS -->
   <?php foreach ($libStyles ?? [] as $libStyle): ?>
      <link rel="stylesheet" href="<?= asset(e($libStyle)) ?>">
   <?php endforeach; ?>

   <!-- General Styles -->
   <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">

   <!-- App CSS -->
   <?php foreach ($styles ?? [] as $style): ?>
      <link rel="stylesheet" href="<?= asset(e($style)) ?>">
   <?php endforeach; ?>

</head>

<body>
   <script>
      const baseUrl = '<?= $baseUrl ?>';
      const sessionCsrfToken = '<?= $sessionCsrfToken ?? '' ?>';
      <?php if (isset($sessionUser) && $sessionUser): ?>
         const sessionUser = '<?= json_encode($sessionUser) ?>';
         const profileId = '<?= $sessionUser['profile_id'] ?>';
      <?php endif; ?>
   </script>


   <!-- Header -->
   <?php require_once __DIR__ . '/header.php'; ?>

   <?php if (!empty($sessionUser)): ?>
      <section class="container-fluid">
         <div class="row">

            <!-- Sidebar -->
            <?php require_once __DIR__ . '/sidebar.php'; ?>

            <!-- Vista -->
            <div class="col-12 col-lg-10 paddingx-0">
               <?= $content ?>
            </div>
         </div>
      </section>
   <?php else: ?>
      <!-- Vista -->
      <?= $content ?>
   <?php endif; ?>

   <!-- Footer -->
   <?php require_once __DIR__ . '/footer.php'; ?>

   <!-- JQuery -->
   <script src="<?= asset('lib/jquery/jquery-3.7.1.min.js') ?>"></script>
   <!-- JQuery Validation -->
   <script src="<?= asset('lib/jquery-validation/jquery.validate.min.js') ?>"></script>
   <script src="<?= asset('lib/jquery-validation/additional-methods-custom.js') ?>"></script>
   <script src="<?= asset('lib/jquery-validation/localization/messages_es.min.js') ?>"></script>
   <!--Bootstrap JS-->
   <script src="<?= asset('lib/bootstrap/bootstrap.bundle.min.js') ?>"></script>
   <!--Sweet Alert 2 JS-->
   <script src="<?= asset('lib/sweetalert/sweetalert2.min.js') ?>"></script>

   <!-- Library JS -->
   <?php foreach ($libScripts ?? [] as $libScript): ?>
      <script src="<?= asset(e($libScript)) ?>"></script>
   <?php endforeach; ?>

   <!-- General JS -->
   <script src="<?= asset('js/scripts.js') ?>"></script>

   <!-- App JS -->
   <?php foreach ($scripts ?? [] as $script): ?>
      <script src="<?= asset(e($script)) ?>"></script>
   <?php endforeach; ?>

</body>

</html>