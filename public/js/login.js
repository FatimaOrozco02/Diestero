// Sección de eventos
$("#loginEmail").on("input", function() {
    $(this).val(formatterObj.setFormat($(this).val()).noSpaces().getFormat());
});

$("#loginPassword").on("input", function() {
    $(this).val(formatterObj.setFormat($(this).val()).noSpaces().getFormat());
});

// Eventos de submit
$("#loginSystem").on("submit", async function(e) {
    e.preventDefault();

    const submitter = e.originalEvent.submitter;
    const action = submitter?.getAttribute("formaction");

    if (!(await validateInfo("loginSystem"))) {
        createAlert("warning", "Campos incompletos", "Por favor, completa todos los campos requeridos.");
        return;
    }

    const data = {
        email: $("#loginEmail").val(),
        password: $("#loginPassword").val()
    };    

    $.ajax({
        url: `${baseUrl}/${action}`,
        method: "POST",
        data: data,
        success: function(response) {
            if (response.authenticated) {
                createAlert("success", "Inicio de sesión exitoso", "");
                location.href = `${baseUrl}/leads`;
            }
        },
        error: function(xhr) {
            if (xhr.status === 401) {
                createAlert("error", "Error de autenticación", "Correo electrónico o contraseña incorrectos.");
            } else {
                createAlert("error", "Error del servidor", "Ocurrió un error al procesar tu solicitud. Por favor, intenta nuevamente más tarde.");
            }
        }
    });
});

$("#loginOutlook").on("click", function() {
    //window.location.href = 'https://login.microsoftonline.com/0a2e7e32-c8cb-4f9c-83a6-8694cc64bb92/oauth2/v2.0/authorize?client_id=977a9a2b-6f10-4a14-8a45-10813d7b601f&response_type=code&redirect_uri=https://ivette-nonseclusive-renato.ngrok-free.dev/CRM_GDC/inicio&response_mode=query&scope=User.Read.All';
    // $.ajax({
    //     url: `${baseUrl}/outlock`,
    //     method: "GET",
    //     success: function(response) {
            
    //     }
    // });
    window.location.href = 'https://login.microsoftonline.com/0a2e7e32-c8cb-4f9c-83a6-8694cc64bb92/oauth2/v2.0/authorize?client_id=977a9a2b-6f10-4a14-8a45-10813d7b601f&response_type=code&redirect_uri=https://057b-200-55-49-2.ngrok-free.app/GDC/login/get_token_outlook&response_mode=query&scope=User.Read.All';
});