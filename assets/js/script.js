$(document).ready(function() {
    $.validator.addMethod('regex', function(value, element, regexp) {
        return this.optional(element) || regexp.test(value);
    }, 'wrong input format');

    $('#main-form').validate({
        submitHandler: function (form) {
            return;
        },
        rules: {
            search: {
                required: true,
            },
            url: {
                required: true,
                regex: /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/
            },
            text: {
                required: true
            }
        },
        messages: {
            url: {
                regex: 'Wrong url format.'
            }
        },
        errorClass: 'error',
        validClass: 'success',
        errorPlacement: function(error, element) {
            var $row = element.closest('.form__row');
            $row.addClass('error').find('.form__row__error').html(error);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).closest('.form__row').addClass(errorClass).removeClass(validClass)
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).closest('.form__row').addClass(validClass).removeClass(errorClass)
        }
    });

    $('[name=search]').on('change', function (e) {
        if ($(this).val() === 'text') {
            $('[name=text]').closest('.form__row').show();
        } else {
            $('[name=text]').closest('.form__row').hide();
        }
    });
});