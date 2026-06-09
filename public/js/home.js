$(document).ready(function() {
   
    const casosExito = [
        { img: `${baseUrl}/img/Logos empresas casos de exito/l1.jpg`, alt: "Mega Label", height: "70px" },
        { img: `${baseUrl}/img/Logos empresas casos de exito/l2.jpg`, alt: "CommLogik", height: "50px" },
        { img: `${baseUrl}/img/Logos empresas casos de exito/l3.jpg`, alt: "CYSAS", height: "40px" },
        { img: `${baseUrl}/img/Logos empresas casos de exito/l4.jpg`, alt: "Pinturerias", height: "45px" },
        
        { img: `${baseUrl}img/Logos empresas casos de exito/l5.jpg`, alt: "Empresa 5", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l6.jpg`, alt: "Empresa 6", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l7.jpg`, alt: "Empresa 7", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l8.jpg`, alt: "Empresa 8", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l9.jpg`, alt: "Empresa 9", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l10.jpg`, alt: "Empresa 10", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l11.jpg`, alt: "Empresa 11", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l12.jpg`, alt: "Empresa 12", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l13.jpg`, alt: "Empresa 13", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l14.jpg`, alt: "Empresa 14", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l15.jpg`, alt: "Empresa 15", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l16.jpg`, alt: "Empresa 16", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l17.jpg`, alt: "Empresa 17", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l18.jpg`, alt: "Empresa 18", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l19.jpg`, alt: "Empresa 19", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l20.jpg`, alt: "Empresa 20", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l21.jpg`, alt: "Empresa 21", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l22.jpg`, alt: "Empresa 22", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l23.jpg`, alt: "Empresa 23", height: "50px" },
        { img: `${baseUrl}img/Logos empresas casos de exito/l24.jpg`, alt: "Empresa 24", height: "50px" }
    ];

    const logosPorBloque = 4;
    const totalBloques = Math.ceil(casosExito.length / logosPorBloque);
    let bloqueActual = 0;
    
   
    const baseUrlJS = "<?php echo $baseUrl ?>";

    
    function inicializarDots() {
        let dotsHTML = '';
        for (let i = 0; i < totalBloques; i++) {
           
            let opacity = (i === 0) ? '0.8' : '0.3';
            dotsHTML += `<span class="dot-indicador rounded-circle bg-secondary" data-bloque="${i}" style="width: 10px; height: 10px; opacity: ${opacity}; cursor: pointer; transition: opacity 0.3s;"></span>`;
        }
        $('#contenedor-dots').html(dotsHTML);
    }

    
    function mostrarBloque(indiceBloque) {
        const inicio = indiceBloque * logosPorBloque;
        const fin = inicio + logosPorBloque;
        const subListaLogos = casosExito.slice(inicio, fin);

        let htmlLogos = '';
        subListaLogos.forEach(logo => {
            htmlLogos += `
                <div class="col-6 col-md-3">
                    <img src="${logo.img}" alt="${logo.alt}" class="img-fluid object-fit-contain" style="max-height: ${logo.height};">
                </div>
            `;
        });

       
        $('#contenedor-logos').fadeOut(300, function() {
            $(this).html(htmlLogos).fadeIn(300);
        });

        
        $('.dot-indicador').css('opacity', '0.3');
        $(`.dot-indicador[data-bloque="${indiceBloque}"]`).css('opacity', '0.8');
    }

    
    let temporizadorLogos = setInterval(function() {
        bloqueActual = (bloqueActual + 1) % totalBloques;
        mostrarBloque(bloqueActual);
    }, 4000);

    
    $(document).on('click', '.dot-indicador', function() {
        clearInterval(temporizadorLogos); 
        bloqueActual = $(this).data('bloque');
        mostrarBloque(bloqueActual);
        
        
        temporizadorLogos = setInterval(function() {
            bloqueActual = (bloqueActual + 1) % totalBloques;
            mostrarBloque(bloqueActual);
        }, 4000);
    });

    
    inicializarDots();
    mostrarBloque(bloqueActual);
});
