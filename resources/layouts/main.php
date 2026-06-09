<!DOCTYPE html>
<html lang="<?= e(app_locale()) ?>">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title><?= $title ?? "CRM" ?></title>
      <link rel="stylesheet" href="<?= publicUrl('lib/Bootstrap/css/bootstrap.css') ?>">
      <link rel="stylesheet" href="<?= publicUrl('lib/SweetAlert/sweetalert2.min.css') ?>">
      <link rel="stylesheet" href="<?= publicUrl('lib/fontawesome/css/all.css') ?>">
      <link rel="stylesheet" href="<?= publicUrl('css/global.css') ?>">


      <!-- Library CSS -->
      <?php foreach ($libStyles ?? [] as $libStyle): ?>
            <link rel="stylesheet" href="<?= publicUrl($libStyle) ?>">
      <?php endforeach; ?>

      <!-- App CSS -->
      <?php foreach ($styles ?? [] as $style): ?>
            <link rel="stylesheet" href="<?= publicUrl($style) ?>">
      <?php endforeach; ?>
</head>

<body>
      <!-- Header -->
      <?php require_once __DIR__ . '/header.php'; ?>

      <!-- Vista -->
      <?= $content ?>

      <!-- Footer -->
      <?php require_once __DIR__ . '/footer.php'; ?>

      <script>
            const baseUrl = "<?= $baseUrl ?>";
      </script>
      <script src="<?= publicUrl('lib/jQuery/jquery-4.0.0.min.js') ?>"></script>
      <script src="<?= publicUrl('lib/Bootstrap/js/bootstrap.bundle.js') ?>"></script>
      <script src="<?= publicUrl('lib/SweetAlert/sweetalert2.all.min.js') ?>"></script>
      <script src="<?= publicUrl('lib/fontawesome/js/all.js') ?>"></script>
      <script src="<?= publicUrl('js/formatter.js') ?>"></script>
      <script src="<?= publicUrl('js/global.js') ?>"></script>

      <!-- App Libs JS -->
      <?php foreach ($libScripts ?? [] as $libScript): ?>
            <script src="<?= publicUrl($libScript) ?>"></script>
      <?php endforeach; ?>

      <!-- App JS -->
      <?php foreach ($scripts ?? [] as $script): ?>
            <script src="<?= publicUrl($script) ?>"></script>
      <?php endforeach; ?>
</body>

</html>