$(document).ready(function() {
    $('#form-contacto').on('submit', function(e) {
        e.preventDefault(); 

        const $btn = $('#btn-enviar');
        const $alertContainer = $('#alert-container');

        // Cambiar estado del botón para indicar progreso
        $btn.prop('disabled', true).text('Enviando...');
        $alertContainer.empty(); 

        
        $.ajax({
            url: `${baseUrl}contacto`,
            type: 'POST',
            data: new FormData(this),
            dataType: 'json',
            processData: false, // Obligatorio para enviar FormData correctamente
            contentType: false, // Obligatorio para enviar FormData correctamente
            success: function(data) {
                $btn.prop('disabled', false).text('Enviar mensaje');

                if (data.status === 'success') {
                    // Alerta verde de éxito
                    $alertContainer.html(`
                        <div style="padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 20px;">
                            ${data.message}
                        </div>
                    `);
                    $('#form-contacto')[0].reset(); // Limpia los inputs del formulario
                } else {
                    // Alerta roja de error controlado (validaciones/SMTP)
                    $alertContainer.html(`
                        <div style="padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 20px;">
                            ${data.message}
                        </div>
                    `);
                }
            },
            error: function() {
                $btn.prop('disabled', false).text('Enviar mensaje');
                // Alerta roja por fallas críticas del servidor o formato inválido
                $alertContainer.html(`
                    <div style="padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 20px;">
                        Error al procesar la solicitud. Intente nuevamente.
                    </div>
                `);
            }
        });
    });
});