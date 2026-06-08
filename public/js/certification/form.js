'use strict';

$(document).ready(() => {

   console.log("hola");

   const certificationForm = $("#certificationForm");
   const certificationIdInput = $("#certificationIdInput");
   const certificationFormSubmitBtn = $("#certificationFormSubmitBtn");

   // Activar editor TinyMCS
   tinymce.init({
      selector: '#contentTextarea',
      license_key: 'gpl',
      plugins: 'link lists',
      height: 300
   });

   // Envio de formulario
   certificationForm.validate({
      submitHandler: function (form) {
         tinymce.triggerSave();
         const certificationId = certificationIdInput.val();
         const formData = new FormData(form);
         if(certificationId) {
            formData.append('_method', 'PUT');
         }
         certificationFormSubmitBtn.attr('disabled', true);
         $.ajax({
            type: "POST",
            url: `${baseUrl}admin/certificaciones${certificationId ? `/${certificationId}` : ''}`,
            data: formData,
            processData: false,
            contentType: false,
         }).done(function (response) {
            try {
               const result = JSON.parse(response);
               if (result.success) {
                  mainAlert('success', result.message);
                  setTimeout(() => {
                     location.reload();
                  }, 1500);
               } else {
                  mainAlert('error', 'Surgio un error', result.message);
               }
            } catch (error) {
               mainAlert('error', 'Surgio un error inesperado');
            }
            certificationFormSubmitBtn.attr('disabled', false);
         })
         .fail(function () {
            mainAlert('error', 'Surgio un error en la conexión');
            certificationFormSubmitBtn.attr('disabled', false);
         });
      }
   });
});