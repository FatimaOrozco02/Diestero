// Variables globales
const formatterObj = new formatter();
const tabAsideActive = window.location.pathname.split("/").pop();

// Funciones globales
function validateInfo(formId) {
   return new Promise((resolve, reject) => {
      let errors = 0;

      $(`#${formId}`).find("input, select, textarea").each(function () {
         if (!$(this).hasClass("optional")) {
            if ($(this).val().trim() === "") {
               $(this).addClass("is-invalid").removeClass("is-valid");
               errors++;
            } else {
               $(this).addClass("is-valid").removeClass("is-invalid");
            }
         }
      });

      resolve(errors === 0);
   });
}

// Limpia los campos de un formulario y remueve las clases de validación
function clearForm(formId = null) {
    if (formId !== null) {
        $(`#${formId}`).find("input, select, textarea").each(function() {
            $(this).val("").removeClass("is-valid is-invalid");
        });
    } else {
        $("input, select, textarea").each(function() {
            $(this).val("").removeClass("is-valid is-invalid");
        });
    }
}

function createAlert(type, title, message) {
   Swal.fire({
      icon: type,
      title: title,
      html: message
   });
}

// function getAsideMenu() {
//    if (window.location.pathname === "/GDC/") {

//       return;
//    }

//    $.ajax({
//       url: `${baseUrl}api/sections`,
//       method: "GET",
//       success: function (response) {
//          response.forEach((section, index) => {
//             const list = $(".nav-list");

//             const link = $("<a>").addClass("nav-link text-white fs-5")
//                .attr("href", section.url)
//                .on("click", function (event) {
//                   selectAsideOption(event);
//                });

//             const icon = $("<i>").addClass(`${section.icon}`);
//             link.append(icon).append(` ${section.name}`);

//             if (section.url.split("/").pop() === tabAsideActive) {
//                link.addClass("active");
//             }

//             list.append(link);
//          });
//       },
//       error: function (xhr, status, error) {
//          console.error("Error al obtener el menú lateral:", error);
//       }
//    })
// }

function selectAsideOption(event) {
   const clicked = $(event.currentTarget);
   const parent = clicked.closest("nav");

   parent.find(".nav-link").removeClass("active");

   clicked.addClass("active");
}

function toggleAside(event) {
   const btnMenu = $(event.currentTarget);
   const i = btnMenu.find("i").toggleClass("fa-bars fa-xmark");

   if (i.hasClass("fa-xmark")) {
      $("aside").attr("style", "display: block !important");
   } else {
      $("aside").attr("style", "display: none !important");
   }
}

function getRandomColor() {
   const letters = "0123456789ABCDEF";
   let color = "#";

   for (let i = 0; i < 6; i++) {
      color += letters[Math.floor(Math.random() * 16)];
   }

   return color;
}

function hideModal(modalId) {
   const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
   modal.hide();
}

function showModal(modalId) {
   const modal = new bootstrap.Modal(document.getElementById(modalId));
   modal.show();
}

// Sección de eventos

// Sección para mostrar el loader al utilizar ajax y ocultarlo al finalizar la petición
$(document).ajaxSend(function (event, jqXHR, sttings) {
   $(".loader-bg").removeClass("d-none");
});

$(document).ajaxComplete(function (event, jqXHR, settings) {
   $(".loader-bg").addClass("d-none");
});

$(document).ajaxError(function (event, jqXHR, settings, thrownError) {
   $(".loader-bg").addClass("d-none");
});


// $(document).ready(function() {
//     $('.navbar-nav .nav-link').on('click', function() {
//         // Quitamos la clase activa a todos
//         $('.navbar-nav .nav-link').removeClass('active');
//         // Se la añadimos únicamente al que se le dio clic para ponerlo rojo
//         $(this).addClass('active');
//     });
// });


$(document).ready(function() {
    
    let urlActual = window.location.href;

    
    $('.navbar-nav .nav-link').removeClass('active');

   
    let coincidenciaEncontrada = false;

   
    $('.navbar-nav .nav-link').each(function() {
        let linkHref = $(this).attr('href');

        // Si la URL actual termina con el href del enlace (ej: /contacto o /cfoaas)
        if (linkHref && urlActual.endsWith(linkHref)) {
            $(this).addClass('active');
            coincidenciaEncontrada = true;
            return false; // Detiene el bucle .each()
        }
    });

    
    if (!coincidenciaEncontrada) {
        $('.navbar-nav .nav-link').first().addClass('active');
    }
});

