<div class="w-100 bg-white">

    <!-- 1. BANNER DE BIENVENIDA COMPACTO -->
    <section style="background: linear-gradient(rgba(255, 255, 255, 0.45), rgba(255, 255, 255, 0.55)), url('<?php echo $baseUrl ?>img/banner 2.jpg') no-repeat center center; background-size: cover; min-height: 140px;" class="d-flex align-items-center border-bottom border-light">
        <div class="container-fluid px-3 px-md-5 py-4 text-center text-md-start">
            <div class="mx-auto" style="max-width: 1440px; width: 100%;">
                <h1 class="text-uppercase mb-1 fw-bold" style="color: #8D3C45; font-size: 2.5rem; letter-spacing: 1px;">CONTACTO</h1>
                <h5 class="text-uppercase mb-0 fw-bold" style="color: #1E355E; font-size: 0.95rem; letter-spacing: 0.5px;">CONVERSEMOS SOBRE EL FUTURO DE TU ORGANIZACIÓN</h5>
            </div>
        </div>
    </section>

    <div class="mx-auto" style="max-width: 1440px; ">

        <section class="py-4 bg-white" style="color: #333333;">
            <div class="container-fluid px-3 px-md-5 mb-5">

                <div class="row mb-4">
                    <div class="col-12 text-muted" style="font-size: 1.05rem; max-width: 900px; line-height: 1.6;">
                        <p class="mb-0">
                            Ya sea que busques financiamiento, certificaciones o soluciones para fortalecer tu operación, estamos listos para ayudarte.
                        </p>
                    </div>
                </div>

                
                <div class="row g-4 align-items-stretch " style="min-height: 500px;">

                    <!-- Columna Izquierda: Imagen Adaptativa -->
                    <div class="col-12 col-md-6 position-relative">
                        <!-- Imagen en Escritorio: Ocupa el espacio de la tarjeta de forma fluida -->
                        <img src="<?php echo $baseUrl ?>img/call-1.jpg" alt="Contacto Diestro" class="w-100 h-100 object-fit-cover shadow-sm rounded-1 d-none d-md-block position-absolute top-0 start-0">
                        <!-- Imagen en Móvil: Se ajusta arriba de forma natural con su separación -->
                        <img src="<?php echo $baseUrl ?>img/call-1.jpg" alt="Contacto Diestro" class="w-100 h-auto d-block d-md-none object-fit-cover shadow-sm rounded-1" style="max-height: 250px;">
                    </div>

                    <!-- Columna Derecha: Formulario -->
                    <div class="col-12 col-md-6">
                        <div class="p-4 p-lg-5 shadow-sm rounded-1 h-100" style="background-color: #f4f6f8;">
                            <form action="#" method="POST">

                                <!-- Nombre -->
                                <div class="mb-3">
                                    <label for="nombre" class="form-label small fw-semibold text-muted mb-1">Nombre</label>
                                    <input type="text" class="form-control rounded-1 border-0 py-2 shadow-sm" id="nombre" name="nombre" required style="font-size: 0.95rem;">
                                </div>

                                <!-- Correo -->
                                <div class="mb-3">
                                    <label for="correo" class="form-label small fw-semibold text-muted mb-1">Correo electrónico</label>
                                    <input type="email" class="form-control rounded-1 border-0 py-2 shadow-sm" id="correo" name="correo" required style="font-size: 0.95rem;">
                                </div>

                                <!-- Asunto -->
                                <div class="mb-3">
                                    <label for="asunto" class="form-label small fw-semibold text-muted mb-1">Asunto</label>
                                    <input type="text" class="form-control rounded-1 border-0 py-2 shadow-sm" id="asunto" name="asunto" required style="font-size: 0.95rem;">
                                </div>

                                <!-- Mensaje -->
                                <div class="mb-4">
                                    <label for="mensaje" class="form-label small fw-semibold text-muted mb-1">Mensaje</label>
                                    <textarea class="form-control rounded-1 border-0 py-2 shadow-sm" id="mensaje" name="mensaje" rows="4" required style="font-size: 0.95rem;"></textarea>
                                </div>

                                <!-- Botón Enviar -->
                                <div class="pt-1">
                                    <button type="submit" class="btn text-white w-100 py-2 fw-semibold rounded-1 transition-all shadow-sm" style="background-color: #8D3C45; font-size: 0.95rem; border: none;">
                                        Enviar mensaje
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>

                </div> 

            </div>
        </section>

    </div> 
</div>
