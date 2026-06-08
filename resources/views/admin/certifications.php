<?php 
/** @var string $baseUrl */
?>

<div class="container py-5">
   <h1 class="text-center">Certificaciones</h1>

   <div class="mb-3">
      <a href="<?= $baseUrl ?>admin/certificaciones/crear" type="button" class="btn btn-main">Nuevo Certificado</a>
   </div>

   <!-- Table -->
   <table id="certificationsTable" class="table">
      <thead>
         <tr>
            <th>Institución</th>
            <th>Certificación</th>
            <th>Código</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th>Estatus</th>
            <th>Creación</th>
            <th>Acciones</th>
         </tr>
      </thead>
   </table>
</div>