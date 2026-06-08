<!DOCTYPE html>
<html lang="<?= e(app_locale()) ?>">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?= $title ?? "CRM" ?></title>
   <link rel="stylesheet" href="<?= publicUrl('lib/bootstrap/css/bootstrap.css') ?>">
   <link rel="stylesheet" href="<?= publicUrl('lib/sweetAlert/sweetalert2.min.css') ?>">
   <link rel="stylesheet" href="<?= publicUrl('lib/fontawesome/css/all.css') ?>">
   <link rel="stylesheet" href="<?= publicUrl('css/global.css') ?>">
   <link rel="stylesheet" href="<?= publicUrl('lib/DataTables/css/datatables.css') ?>">
   

   <!-- Library CSS -->
   <?php foreach ($libStyles ?? [] as $libStyle): ?>
      <link rel="stylesheet" href="<?= $libStyle ?>">
   <?php endforeach; ?>

   <!-- App CSS -->
   <?php foreach ($styles ?? [] as $style): ?>
      <link rel="stylesheet" href="<?= $style ?>">
   <?php endforeach; ?>
</head>

<body>
   <div class="loader-bg d-none">
      <span class="loader"></span>
   </div>

   <!-- Header -->
   <?php require_once __DIR__ . '/header.php'; ?>

   <?php if ($sessionUserProfileId && $sessionLayoutAside): ?>
      <section>
         <div class="d-flex mxw-100 row-max-width-responsive">
            <!-- Sidebar -->
            <?php if ($sessionLayoutAside): ?>
               <?php require_once __DIR__ . "/sidebar.php"; ?>
            <?php endif; ?>

            <!-- Vista -->
            <div class="w-85 p-0 ml-15p">
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

   <script>
      const baseUrl = "<?= $baseUrl ?>";
   </script>
   <script src="<?= publicUrl('lib/jQuery/jquery-4.0.0.min.js') ?>"></script>
   <script src="<?= publicUrl('lib/bootstrap/js/bootstrap.bundle.js') ?>"></script>
   <script src="<?= publicUrl('lib/sweetAlert/sweetalert2.all.min.js') ?>"></script>
   <script src="<?= publicUrl('lib/fontawesome/js/all.js') ?>"></script>
   <script src="<?= publicUrl('js/formatter.js') ?>"></script>
   <script src="<?= publicUrl('js/global.js') ?>"></script>
   <script src="<?= publicUrl('lib/DataTables/js/datatables.js') ?>"></script>

   <!-- App Libs JS -->
   <?php foreach ($libScripts ?? [] as $libScript): ?>
      <script src="<?= $libScript ?>"></script>
   <?php endforeach; ?>

   <!-- App JS -->
   <?php foreach ($scripts ?? [] as $script): ?>
      <script src="<?= $script ?>"></script>
   <?php endforeach; ?>
</body>

</html>