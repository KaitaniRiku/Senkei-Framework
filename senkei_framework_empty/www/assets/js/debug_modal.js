$(document).ready(function(){
    var $modalContainer = $('#modal_container');

    $('.btn_debug').on('click', function(){
        var $modal_id = $('#' + $(this).attr('modal'));
        modal($modal_id);
    });

    $('.btn_close_modal').on('click', function(){
        closeModal($(this).closest('.debug_item'));
    });

    function modal($element){
        $modalContainer.fadeIn(200, function(){
            $element.addClass('position_default').fadeIn(300);
            $element.animate({
                'top' : '50px'
            }, 300);
        });
    }

    function closeModal($element){
        $element.animate({
            'top' : $(window).height()
        }, 150, function(){
            $element.fadeOut(100, function(){
                $modalContainer.fadeOut(200);
            });
        });
    }
});
