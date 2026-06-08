$(document).ready(function() {
    
    $('#acquisitions, #financing, #business, #pharma').hide();

    
    $('.interactive-card').click(function() {
        
        let vistaObjetivoId = $(this).data('target');
        
        
        if (vistaObjetivoId === 'acquisitions') {
            vistaObjetivoId = 'acquisitions';
        }
        
        
        $('#principal').hide();
        
        
        $('#' + vistaObjetivoId).fadeIn();
    });
});
