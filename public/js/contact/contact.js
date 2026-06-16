$(document).ready(function() {
    $('#form-contacto').on('submit', function(e) {
        e.preventDefault(); 

        const $btn = $('#btn-enviar');
        const $alertContainer = $('#alert-container');

        
        $btn.prop('disabled', true).text('Enviando...');
        $alertContainer.empty(); 

        
        $.ajax({
            url: `${baseUrl}contacto`,
            type: 'POST',
            data: new FormData(this),
            dataType: 'json',
            processData: false, 
            contentType: false, 
            success: function(data) {
                $btn.prop('disabled', false).text('Enviar mensaje');

                if (data.status === 'success') {
                    
                    $alertContainer.html(`
                        <div style="padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px; margin-bottom: 20px;">
                            ${data.message}
                        </div>
                    `);
                    $('#form-contacto')[0].reset(); 
                } else {
                    
                    $alertContainer.html(`
                        <div style="padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 20px;">
                            ${data.message}
                        </div>
                    `);
                }
            },
            error: function() {
                $btn.prop('disabled', false).text('Enviar mensaje');
                
                $alertContainer.html(`
                    <div style="padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 20px;">
                        Error al procesar la solicitud. Intente nuevamente.
                    </div>
                `);
            }
        });
    });
});