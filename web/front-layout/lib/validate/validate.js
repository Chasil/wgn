            (function ( $ ) {

                var hasErrors, field;

                $.fn.validate = function(options) {
                    var settings = $.extend({
                        sendValid:true,
                    }, options );
                    $(this).on('focusout',':input',function(){
                        field = $(this);
                        console.log(filed);
                        validateField();
                    });
                    $(this).on('change',':checkbox',function(){
                        field = $(this);
                        validateField();
                    });
                    $(this).on('submit',function(e) {
                        hasErrors = false;
                        $(this).find('.form-group').removeClass('has-error');
                        $(this).find('.form-group span.help-block').remove();

                        $(this).find(':input[data-validator]').each(function(){
                            field = $(this);
                            var fieldHasErrors = validateField();

                            if(!hasErrors && fieldHasErrors==false){
                                hasErrors = true;
                            }
                        });

                        if(hasErrors){
                            $(this).trigger("form:invalid");
                            e.preventDefault();
                            return false;
                        }
                        $(this).trigger("form:valid");
                        if(!settings.sendValid){
                            e.preventDefault();
                        }
                        return true;
                    });
                    function validateField(){
                            var validators = field.attr('data-validator');
                            field.closest('.form-group').find('span.help-block').remove();
                            field.closest('.form-group').removeClass('has-error');
                            var fieldHasErrors = false;

                            if(!validators){
                                return false;
                            }
                            validators = validators.split(',');
                            for(var i = 0;i < validators.length;i++){
                                if(!isValid(validators[i])){
                                    fieldHasErrors = true;
                                }
                            }

                            if(fieldHasErrors){
                                addErrorClass();
                                field.attr('data-valid','false');

                                return false;
                            }

                            return true;
                    }
                    function isValid(validator){
                        return eval("validator"+ucfirst(validator)+"()");
                    }
                    function validatorRequired(){

                        if(field[0].type==='checkbox' || field[0].type ==='radio'){
                            return choiceTypeRequired();
                        }

                        return textTypeRequired();
                    }
                    function textTypeRequired(){
                        if(field.val()!==''){
                            return true;
                        }
                        addErrorMessage('Pole do uzupełnienia.');
                        return false;
                    }
                    function choiceTypeRequired(){
                        if(field[0].checked){
                            return true;
                        }
                        addErrorMessage('Pole musi być zaznaczone.');
                        return false;
                    }
                    function validatorEmail(){
                        if(field.val()===''){
                            return true;
                        }
                        var format = /^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-.]+$/;
                        if(field.val().match(format)){
                            return true;
                        }
                        addErrorMessage('Podany adres jest niepoprawny.');
                        return false;
                    }
                    function validatorLength(){
                        var params = field.data('validator-length');

                        params = params.split(',');
                        if(params[0]!=='' && params[1]!==''){
                            if(field.val().length >= params[0] && field.val().length <= params[1]){

                                return true;
                            }
                        }else if(params[0]==='' && params[1]!==''){
                            if(field.val().length <= params[1]){
                                return true;
                            }
                        }else if(params[0]!=='' && params[1]===''){
                            if(field.val().length >= params[0]){
                                return true;
                            }
                        }
                        addErrorMessage('Pole ma nieprawidłową długość.');
                        return false;
                    }
                    function validatorFloat(){

                        if(field.val()===''){
                            return true;
                        }
                        var value = field.val().replace(',','.');

                        if(!isNaN(parseFloat(value))){
                            return true;
                        }
                        addErrorMessage('Pole nie jest liczbą.');
                        return false;
                    }
                    function validatorNumber(){
                        if(field.val()===''){
                            return true;
                        }
                        if(!isNaN(parseInt(field.val()))){
                            return true;
                        }
                        addErrorMessage('Pole nie jest liczbą.');
                        return false;
                    }
                    function addErrorClass(){
                        field.closest('.form-group').addClass('has-error');

                    }
                    function addErrorMessage(message){
                        if(field.closest('.form-group').has('span.help-block').length > 0){
                            field.closest('.form-group').find('span.help-block ul').append('<li><span class="glyphicon glyphicon-exclamation-sign"></span> '+message+'</li>');
                        }else {
                            field.closest('.form-group').append('<span class="help-block"><ul class="list-unstyled"><li><span class="glyphicon glyphicon-exclamation-sign"></span> '+message+'</li></ul></span>')
                        }
                    }
                    function ucfirst(string) {
                        return string.charAt(0).toUpperCase() + string.slice(1);
                    }
                };

            }( jQuery ));


