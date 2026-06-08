<div class="w-100 d-flex justify-content-center mt-5">
    <form id="loginSystem" method="post" class="w-50">
        <div class="bg-bi-color-blue p-3 rounded d-flex flex-wrap gap-3">
            <div class="w-100 d-flex justify-content-center">
                <div class="logo-gdc">
                    <img src="img/crm_gdc/logo_gdc.jpg" alt="Logo GDC">
                </div>
            </div>
            <div class="group-floating w-100">
                <input id="loginEmail" type="email" class="form-control f-input" placeholder="Correo electrónico">
                <label for="loginEmail" class="f-label">Correo electrónico</label>
            </div>
            <div class="group-floating w-100">
                <input id="loginPassword" type="password" class="form-control f-input" placeholder="Contraseña">
                <label for="loginPassword" class="f-label">Contraseña</label>
            </div>
            <div class="w-100 d-flex justify-content-center align-items-center gap-3">
                <button type="submit" formaction="login/simple" class="btn btn-dark-blue w-20">Ingresar</button>
                <button id="loginOutlook" type="button" formaction="login/outlook" class="btn btn-primary w-20">Ingresar Outlook</button>
            </div>
        </div>
    </form>
</div>