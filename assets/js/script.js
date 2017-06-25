$(document).ready(function() {
    function showErrors(errors) {
        for (var e in errors) {
            if (!errors.hasOwnProperty(e)) {
                continue;
            }

            var $input = $('input[name=' + e + ']');
            if (!$input.length) {
                continue;
            }

            $input.closest('.form__row')
                .removeClass('success')
                .addClass('error')
                .find('.form__row__error')
                .html('<label>' + errors[e] + '</label>');
        }
    }

    $.validator.addMethod('regex', function(value, element, regexp) {
        return this.optional(element) || regexp.test(value);
    }, 'wrong input format');

    $('#main-form').validate({
        submitHandler: function (form) {
            $('#main-result').hide();
            $.ajax({
                url: '/api/v1/url/add',
                method: 'POST',
                dataType: 'json',
                data: $(form).serialize(),
                success: function(result) {
                    if (result.status) {
                        $('#main-result').show();
                    } else {
                        showErrors(result.errors);
                    }
                }
            })
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
            search: {
                required: validationMessages.notEmpty
            },
            url: {
                required: validationMessages.notEmpty,
                regex: validationMessages.urlRegex
            },
            text: {
                required: validationMessages.notEmpty
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

    $('[name=type]').on('change', function (e) {
        if ($(this).val() === 'text') {
            $('[name=text]').closest('.form__row').show();
        } else {
            $('[name=text]').closest('.form__row').hide();
        }
    });
});