<div class="w-100 bg-white">

    <section class="py-5 font-sans-serif" style="background: linear-gradient(rgba(255, 255, 255, 0.45), rgba(255, 255, 255, 0.55)), url('<?php echo $baseUrl ?>img/banner 2.jpg') no-repeat center center; background-size: cover;">
        <div class="container-fluid px-5 py-5">

            <h1 class="text-uppercase fw-bold mb-1" style="color: #8D3C45; font-size: 2.5rem; letter-spacing: 1px;">Contacto</h1>

            <h5 class="text-uppercase fw-bold mb-4" style="color: #1E355E; font-size: 1rem; letter-spacing: 0.5px;">Conversemos sobre el futuro de tu organización</h5>
        </div>
    </section>

    <section class="py-5 bg-white font-sans-serif" style="color: #333333;">
        <div class="container-fluid px-5">

            <!-- Texto descriptivo introductorio -->
            <div class="row mb-5">
                <div class="col-12 text-muted" style="font-size: 0.95rem; max-width: 900px;">
                    <p class="mb-0">
                        Ya sea que busques financiamiento, certificaciones o soluciones para fortalecer tu operación, estamos listos para ayudarte.
                    </p>
                </div>
            </div>

            
            <div class="row g-0 shadow-sm border" style="min-height: 500px;">

                <div class="col-12 col-md-6  position-relative">
                    <img src="<?php echo $baseUrl ?>img/call-1.jpg" alt="Contacto Diestro" class="w-100 h-100 object-fit-cover position-absolute top-0 start-0 d-none d-md-block">
                    <!-- Imagen visible solo en móviles para mantener el diseño adaptativo -->
                    <img src="<?php echo $baseUrl ?>img/call-1.jpg" alt="Contacto Diestro" class="w-100 h-auto d-block d-md-none object-fit-cover" style="max-height: 250px;">
                </div>

                <!-- Columna Derecha: Formulario -->
                <div class="col-12 col-md-6 p-4 p-lg-5" style="background-color: #f4f6f8;">
                    <form >

                        <!-- CNombre -->
                        <div class="mb-3">
                            <label for="nombre" class="form-label small fw-semibold text-muted mb-1">Nombre</label>
                            <input type="text" class="form-control rounded-0 border-0 py-2 shadow-sm" id="nombre" name="nombre" required>
                        </div>

                        <!-- Correo -->
                        <div class="mb-3">
                            <label for="correo" class="form-label small fw-semibold text-muted mb-1">Correo</label>
                            <input type="email" class="form-control rounded-0 border-0 py-2 shadow-sm" id="correo" name="correo" required>
                        </div>

                        <!-- Asunto -->
                        <div class="mb-3">
                            <label for="asunto" class="form-label small fw-semibold text-muted mb-1">Asunto</label>
                            <input type="text" class="form-control rounded-0 border-0 py-2 shadow-sm" id="asunto" name="asunto" required>
                        </div>

                        <!-- Mensaje -->
                        <div class="mb-4">
                            <label for="mensaje" class="form-label small fw-semibold text-muted mb-1">Mensaje</label>
                            <textarea class="form-control rounded-0 border-0 py-2 shadow-sm" id="mensaje" name="mensaje" rows="4" required></textarea>
                        </div>

                        <!-- Botón Enviar -->
                        <div class="pt-2">
                            <button type="submit" class="btn text-white w-100 py-2 fw-semibold rounded-2 transition-all shadow-sm" style="background-color: #8D3C45; font-size: 0.95rem; border: none;">
                                Enviar
                            </button>
                        </div>

                    </form>
                </div>

            </div>

        </div>
    </section>

</div>