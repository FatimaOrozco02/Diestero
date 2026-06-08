<div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center bg-main-2">
      <div class="row w-100 justify-content-center">
            <div class="col-11 col-sm-10 col-md-8 col-lg-5 col-xl-4">
                  <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <!-- Header -->
                        <div class="bg-main- text-white- text-center p-3">
                              <h2 class="fw-bold mb-1">Administrador</h2>
                        </div>

                        <!-- Body -->
                        <div class="card-body p-4">
                              <form id="loginForm">

                                    <?= csrf_field() ?>

                                    <div class="mb-4">
                                          <label for="usernameInput" class="form-label fw-semibold">Usuario</label>
                                          <input type="text" name="username" id="usernameInput" class="form-control form-control-lg" placeholder="Ej: usuario123" minlength="3" maxlength="80" required>
                                    </div>

                                    <div class="mb-4">
                                          <label for="passwordInput" class="form-label fw-semibold">Contraseña </label>
                                          <input type="password" name="password" id="passwordInput" class="form-control form-control-lg" placeholder="Tu contraseña" minlength="3" maxlength="80" required>
                                    </div>

                                    <div class="d-grid mt-5">
                                          <button type="submit" id="loginFormSubmitBtn" class="btn btn-main btn-lg rounded-3"> Ingresar</button>
                                    </div>
                              </form>
                        </div>
                  </div>
            </div>
      </div>
</div>