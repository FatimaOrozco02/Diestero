"use strict";

$(document).ready(() => {
  const certificationForm = $("#certificationForm");
  const certificationFormSubmitBtn = $("#certificationFormSubmitBtn");
  const certificationResultContainer = $("#certificationResultContainer");
  const certificationCode = $("#certificationCode");
  const certificationCertify = $("#certificationCertify");
  const certificationInstitution = $("#certificationInstitution");
  const certificationIssueDate = $("#certificationIssueDate");
  const certificationValidity = $("#certificationValidity");
  const certificationDownloadBtn = $("#certificationDownloadBtn");

  // Envio de formulario
  certificationForm.validate({
    messages: {
      code: {
        required: "Por favor ingrese el código de certificación",
      },
    },
    submitHandler: function (form) {
      certificationResultContainer.addClass("d-none");
      certificationFormSubmitBtn.attr("disabled", true);
      $.ajax({
        type: "POST",
        url: `${baseUrl}certificaciones/verificar`,
        data: certificationForm.serialize(),
      })
        .done(function (response) {
          mainAlert("success", "Código válido", "", false, 1200);
          certificationResultContainer.removeClass("d-none");
          certificationCertify.text(response.data.certify_shortname);
          certificationCode.text(response.data.code);
          certificationInstitution.text(response.data.institution);
          certificationIssueDate.text(response.data.start_date);
          certificationValidity.text(response.data.end_date);
          certificationDownloadBtn.attr("href", `${baseUrl}media/certificaciones/${response.data.id}`);
          certificationDownloadBtn.attr("download", `certificado-${response.data.code}.pdf`);
        })
        .fail(function () {
          mainAlert("error", "Código no válido", "Por favor verifique que el código ingresado sea correcto", true, false);
        })
        .always(function () {
          certificationFormSubmitBtn.attr("disabled", false);
        });
    },
  });
});
