$(document).ready(function () {
    
    $('.interactive-card').on('click', function () {
        const targetId = $(this).attr('data-target');
        const $targetView = $('#' + targetId);

        if ($targetView.length) {
            $('#principal').addClass('d-none'); 
            $targetView.removeClass('d-none');  
        }
    });
    
    $('.btn-back').on('click', function () {
        
        $('.view-section').addClass('d-none');
        
        
        $('#principal').removeClass('d-none');
        
        $(window).scrollTop(0);
    });
});
