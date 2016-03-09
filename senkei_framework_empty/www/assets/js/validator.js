$(document).ready(function(){

    $textFields = $('input, textarea');

    $('form').on('submit', function(){
        $errors = 0;

        $.each($textFields, function(){
            if(typeof $(this).attr('control-empty') !== 'undefined'){
                if(validateEmpty($(this), 'control-empty')){

                    if(typeof $(this).attr('control-string') !== 'undefined'){
                        if(!validateString($(this), 'control-string')){
                            $errors++;
                        }
                    }

                    if(typeof $(this).attr('control-mail') !== 'undefined'){
                        if(!validateMail($(this), 'control-mail')){
                            $errors++;
                        }
                    }
                } else {
                    $errors++;
                }
            }
        });

        console.log($errors);
        return $errors > 0 ? false : true;
    });


    function atLeastOneIsChecked($element) {
        var valid;

        if ($element.length > 0) {
            $('.help_checkbox').text('');
            valid = true;
        } else {
            $('.help_checkbox').css('color', 'red').text('Veuillez cocher au moins une case');
            valid = false;
        }

        return valid;
    }

    function validateMaxChar($element, attrRule){
        if(typeof $element.attr(attrRule) !== 'undefined'){
            var valid;
            var elementValue = $element.val();
            var nbChar = parseInt($element.attr(attrRule));
            if(elementValue.length > nbChar){
                displayError($element, 'Ce champs ne doit pas excéder ' + nbChar + ' caractères', '#BB0B0B');
                valid = false;
            } else {
                displayError($element, '', 'green');
                valid = true;
            }

            return valid;
        }
    }

    function validateMinChar($element, attrRule){
        if(typeof $element.attr(attrRule) !== 'undefined'){
            var valid;
            var elementValue = $element.val();
            var nbChar = parseInt($element.attr(attrRule));
            if(elementValue.length < nbChar){
                displayError($element, 'Ce champs doit comporter un minimum de ' + nbChar + ' caractères', '#BB0B0B');
                valid = false;
            } else {
                displayError($element, '', 'green');
                valid = true;
            }

            return valid;
        }
    }

    function validateNbChar($element, attrRule){
        if(typeof $element.attr(attrRule) !== 'undefined'){
            var valid;
            var elementValue = $element.val();
            var nbChar = parseInt($element.attr(attrRule));
            if(elementValue.length !== nbChar){
                displayError($element, 'Ce champs doit comporter ' + nbChar + ' caractères', '#BB0B0B');
                valid = false;
            } else {
                displayError($element, '', 'green');
                valid = true;
            }

            return valid;
        }
    }

    function validateMail($element, attrRule){
        if(typeof $element.attr(attrRule) !== 'undefined'){
            var valid;
            var elementValue = $element.val();
            var regexMail = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if(!regexMail.test(elementValue)){
                displayError($element, 'Veuillez saisir un mail valide', '#BB0B0B');
                valid = false;
            } else {
                displayError($element, '', 'green');
                valid = true;
            }

            return valid;
        }
    }

    function validateInt($element, attrRule){
        if(typeof $element.attr(attrRule) !== 'undefined'){
            var valid;
            var elementValue = $element.val();
            if(elementValue != parseInt($element.val())){
                displayError($element, 'Veuillez saisir un entier', '#BB0B0B');
                valid = false;
            } else {
                displayError($element, '', 'green');
                valid = true;
            }

            return valid;
        }
    }

    function validateFloat($element, attrRule){
        if(typeof $element.attr(attrRule) !== 'undefined'){
            var valid;
            var elementValue = $element.val();
            if(elementValue != parseFloat(elementValue)){
                displayError($element, 'Veuillez saisir un nombre décimal', '#BB0B0B');
                valid = false;
            } else {
                displayError($element, '', 'green');
                valid = true;
            }

            return valid;
        }
    }

    function validateString($element, attrRule){
        if(typeof $element.attr(attrRule) !== 'undefined'){
            var valid;
            var regexString = /^[a-zA-Z0-9éèàê\s]+$/;

            var elementValue = $element.val();
            if(regexString.test(elementValue) === false){
                displayError($element, 'Veuillez saisir une chaîne de caractère valide (sans caractère spécial)', '#BB0B0B');
                valid = false;
            } else {
                displayError($element, '', 'green');
                valid = true;
            }

            return valid;
        }
    }

    function validateEmpty($element, attrRule){
        if(typeof $element.attr(attrRule) !== 'undefined'){
            var valid;
            if($element.val() === ''){
                displayError($element, 'Ce champ est obligatoire', '#BB0B0B');
                valid = false;
            } else {
                displayError($element, '', 'green');
                valid = true;
            }

            return valid;
        }
    }

    function displayError($element, message, border){
        if(typeof border !== 'undefined'){
            $element.css('border', '1px ' + border + ' solid');
        }
        $element.next().css('color', 'red').text(message);
    }


});
