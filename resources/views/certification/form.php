<?php 
/** @var string $baseUrl */
/** @var array|null $certification */
/** @var array $certifies */
?>

<div class="container py-4">
   <h1 class="mb-4 text-center"><?= (!empty($certification) ? 'Actualizar' : 'Nuevo') ?> certificado</h1>

   <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
         <li class="breadcrumb-item"><a href="<?=$baseUrl?>admin/certificaciones">Certificaciones</a></li>
         <li class="breadcrumb-item active" aria-current="page">Formulario</li>
      </ol>
   </nav>

   <form id="certificationForm" class="row">
      <?= csrf_field() ?>
      
      <input type="hidden" name="certification_id" id="certificationIdInput" value="<?= (!empty($certification) ? $certification['id'] : '') ?>">
      <div class="col-6 mb-3">
         <label for="certifySelect" class="form-label text-main">Certificación <span class="text-danger">*</span></label>
         <select name="certify_id" id="certifySelect" class="form-select" <?= (!empty($certification) ? 'disabled' : 'required') ?>>
            <option value="">Selecciona una opción</option>
            <?php foreach ($certifies as $certify) : ?>
               <option value="<?= $certify['id'] ?>" <?= (!empty($certification) && $certification['certify_id'] == $certify['id']) ? 'selected' : '' ?>><?= $certify['shortname'] ?></option>
            <?php endforeach; ?>
         </select>
      </div>
      <div class="col-6 mb-3">
         <label for="codeInput" class="form-label text-main">Código</label>
         <input type="text" name="code" id="codeInput" class="form-control" value="<?= (!empty($certification) ? $certification['code'] : '') ?>" disabled>
      </div>
      <div class="col-12 mb-3">
         <label for="institutionInput" class="form-label text-main">Institución <span class="text-danger">*</span></label>
         <input type="text" name="institution" id="institutionInput" class="form-control" placeholder="Ej: GDC Difusión Científica, S.A. de C.V." maxlength="150" value="<?= (!empty($certification) ? $certification['institution'] : '') ?>" required>
      </div>
      <div class="col-12 mb-3">
         <label for="addressInput" class="form-label text-main">Dirección <span class="text-danger">*</span></label>
         <input type="text" name="address" id="addressInput" class="form-control" placeholder="Ej: Cuajimalpa, Avenida Insurgentes Sur número 885 Piso 1, C.P. 03840." maxlength="255" value="<?= (!empty($certification) ? $certification['address'] : '') ?>" required>
      </div>
      <div class="col-12 mb-3">
         <label for="contentTextarea" class="form-label text-main">Contenido del certificado <span class="text-danger">*</span></label>
         <textarea name="content" id="contentTextarea" class="form-control" placeholder="Ej: Que ha sido evaluado y cumnple con los requisitos..." minlength="1" maxlength="2000" required><?= (!empty($certification) ? $certification['content'] : '') ?></textarea>
      </div>
      <div class="col-12 col-lg-6 mb-3">
         <label for="certifierInput" class="form-label text-main">Agente certificador <span class="text-danger">*</span></label>
         <input type="text" name="certifier" id="certifierInput" class="form-control" placeholder="Ej: Juan G. Sosa Martínez" maxlength="100" value="<?= (!empty($certification) ? $certification['certifier'] : '') ?>" required>
      </div>
      <div class="col-12 col-lg-6 mb-3">
         <label for="signatureFile" class="form-label text-main">Firma del certificador</label>
         <input type="file" name="signature" id="signatureFile" class="form-control" accept="image/png">
         <?php if (!empty($certification) && !empty($certification['signature'])): ?>
            <div><img src="signatures/<?= $certification['signature'] ?>" alt="Firma" class="mh-5r"></div>
         <?php endif; ?>
      </div>
      <div class="col-6 mb-3">
         <label for="startDateInput" class="form-label text-main">Fecha de inicio</label>
         <input type="date" name="start_date" id="startDateInput" class="form-control" value="<?= (!empty($certification) ? $certification['start_date'] : '') ?>">
      </div>
      <div class="col-6 mb-3">
         <label for="endDateInput" class="form-label text-main">Fecha de fin</label>
         <input type="date" name="end_date" id="endDateInput" class="form-control" value="<?= (!empty($certification) ? $certification['end_date'] : '') ?>">
         <div id="end_dateHelp" class="form-text">Se recomienda de 1 a 3 años a partir de la fecha de inicio.</div>
      </div>
      <div class="col-12 mb-3 text-center">
         <a href="<?= $baseUrl ?>admin/certifications" type="button" class="btn btn-secondary">Cancelar</a>
         <button type="submit" id="certificationFormSubmitBtn" class="btn btn-main"><?= !empty($certification) ? 'Actualizar' : 'Crear' ?></button>
      </div>
   </form>
</div>