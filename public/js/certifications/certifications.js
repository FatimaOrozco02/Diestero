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
          createAndDownloadFile(`${baseUrl}media/certificaciones/${response.data.id}`, `certificado-${response.data.code}.pdf`);
        })
        .fail(function () {
          mainAlert("error", "El certificado no se ha encontrado, por favor verifique el código ingresado");
        })
        .always(function () {
          certificationFormSubmitBtn.attr("disabled", false);
        });
    },
  });
});
