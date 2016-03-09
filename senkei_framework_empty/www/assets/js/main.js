$(document).ready(function(){
    getInfos()

    function getInfos() {
        $.ajax({
            url: 'www/ajax/ajax.php?module=blog&action=getArticles',
            type: 'POST',
            datatype: 'json',
            data: {},
            beforeSend: function () {
                //   $('.loader').show();
            },
            success: function(data) {
              console.log(data);
            },
        });
    }
});
