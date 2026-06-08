"use strict";

$(document).ready(() => {
  // Selectores
  const loginForm = $("#loginForm");
  const usernameInput = $("#usernameInput");
  const passwordInput = $("#passwordInput");
  const loginFormSubmitBtn = $("#loginFormSubmitBtn");

  // Envio de formulario
  loginForm.validate({
    errorClass: "is-invalid",
    validClass: "is-valid",
    errorElement: "div",
    submitHandler: function () {
      loginFormSubmitBtn.prop("disabled", true);

      $.ajax({
        url: `${baseUrl}admin/inicio_sesion`,
        type: "POST",
        dataType: "json",
        data: loginForm.serialize(),
      })
        .done(function (response) {
          try {
            mainAlert(response.success ? "success" : "error", response.message);

            if (response.success) {
              setTimeout(() => {
                window.location.href = `${baseUrl}admin/certificaciones`;
              }, 1000);
            }
          } catch (error) {
            mainAlert("error", "Surgio un error inesperado");
          }
        })
        .fail(function (xhr) {
          mainAlert("error", xhr.responseJSON?.message || "Surgio un error inesperado");
        })
        .always(function () {
          loginFormSubmitBtn.prop("disabled", false);
        });
    },
  });
});
