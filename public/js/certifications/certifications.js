"use strict";

$(document).ready(() => {
  const certificationForm = $("#certificationForm");
  const certificationFormSubmitBtn = $("#certificationFormSubmitBtn");

  // Envio de formulario
  certificationForm.validate({
    submitHandler: function (form) {
      certificationFormSubmitBtn.attr("disabled", true);
      $.ajax({
        type: "POST",
        url: `${baseUrl}certificaciones/verificar`,
        data: certificationForm.serialize(),
      })
        .done(function (response) {
          console.log(response);
          mainAlert("success", "Código encontrado");
          createAndDownloadFile(`${baseUrl}media/certificaciones/${response.data.id}`);
        })
        .fail(function () {
          mainAlert("error", "Surgio un error en la conexión");
        })
        .always(function () {
          certificationFormSubmitBtn.attr("disabled", false);
        });
    },
  });
});
