<!-- <footer class="bg-black text-white p-0 overflow-hidden font-sans-serif w-100" style="height: 180px;">
   
    <div class="container-fluid px-5 h-100">
        
        <div class="row align-items-center h-100 position-relative">
            
           
            <div class="col-4 text-start z-1">
                <div style="max-width: 160px;">
                   <img src="<?php echo $baseUrl ?>img/logo diestro_1200px_blanco.png" alt="Diestro Fortalecemos Negocios" class="img-fluid">
                    
                </div>
            </div>
            
           
            <div class="col-4 h-100 text-center position-relative">
                <div class="h-100 d-inline-block" style="-webkit-mask-image: linear-gradient(to right, transparent 0%, black 20%, black 80%, transparent 100%);
                    mask-image: linear-gradient(to right, transparent 0%, black 20%, black 80%, transparent 100%);">
                    
                    <img src="<?php echo $baseUrl ?>img/toro-impulso.jpg" alt="Impulso" class="img-fluid object-fit-contain">
                </div>
            </div>
            
           
            <div class="col-4 d-none d-md-block"></div>
            
        </div>
        
    </div>
</footer> -->


<footer class="bg-black text-white p-0 overflow-hidden w-100" style="min-height: 140px; height: auto;">
   
    <!-- Contenedor limitado para pantallas ultra anchas (2K/4K) -->
    <div class="container-fluid px-3 px-md-5 mx-auto" style="max-width: 1440px;">
        
        <!-- ESTA FILA SE COMPORTA COMO ESCRITORIO EN PANTALLAS GRANDES -->
        <div class="row align-items-center position-relative" style="min-height: 180px;">
            
            <!-- Columna Izquierda: Logo Blanco (Se alinea al centro en móviles) -->
            <div class="col-12 col-lg-4 text-center text-lg-start z-1 mb-3 mb-lg-0 py-3 py-lg-0">
                <div class="mx-auto mx-lg-0" style="max-width: 160px;">
                   <img src="<?php echo $baseUrl ?>img/logo diestro_1200px_blanco.png" alt="Diestro Fortalecemos Negocios" class="img-fluid">
                </div>
            </div>
            
            <!-- Columna Central: Intercambio de Toros Responsivo -->
            <div class="col-12 col-lg-4 text-center position-relative d-flex align-items-center justify-content-center mb-3 mb-lg-0">
                
                <!-- TORO ANIMAL: d-none d-lg-inline-block (Solo visible de Tablet hacia ARRIBA) -->
                <div class="h-100 d-none d-lg-inline-block" style="-webkit-mask-image: linear-gradient(to right, transparent 0%, black 20%, black 80%, transparent 100%);
                    mask-image: linear-gradient(to right, transparent 0%, black 20%, black 80%, transparent 100%); max-height: 180px;">
                    <img src="<?php echo $baseUrl ?>img/toro-impulso.jpg" alt="Impulso" class="img-fluid object-fit-contain" style="max-height: 180px;">
                </div>

            </div>
            
            <div class="col-4 d-none d-lg-block"></div>
            
        </div>
        
    </div>
</footer>
