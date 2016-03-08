$(document).ready(function(){
    $('input:file').on('change', function(){
        console.log($(this).prop('files')[0]);
    })

    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#apercu').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $('input:file').change(function(){
        readURL(this);
    });
})
