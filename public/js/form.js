$.form = function($form, validateURL) {
    var $inputs = $form.find('input, textarea, select');

    var $submitButton = $form.find('*[name="submit"]');

    if ($submitButton.length == 0)
        console.log('No submit button found. Did you remember the name="submit" attribute for the button?');

    $submitButton.click(function(e, valid) {
        if (valid)
            return true;

        var enctype = $form.attr('enctype') || 'x-www-form-urlencoded';

        var data = {};

        if (enctype === 'multipart/form-data') {
            data = new FormData($form[0]);
        } else {
            for (var input of $inputs) {
                var $elem = $(input);

                var key = $elem.attr('name');
                var value = $elem.val();

                data[key] = value;
            }
        }

        $submitButton.addClass('is-loading');
        $submitButton.blur();

        // http://www.mattlunn.me.uk/blog/2012/05/sending-formdata-with-jquery-ajax/

        var ajaxParams = null;

        if (enctype === 'multipart/form-data') {
            ajaxParams = {
                url: validateURL,
                type: 'POST',
                data: data,
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
            };
        } else {
            ajaxParams = {
                url: validateURL,
                type: 'POST',
                data: data,
            };
        }

        $.ajax(ajaxParams).done(function(data) {
            var errors = data.errors || {};

            $submitButton.removeClass('is-loading');

            for (var input of $inputs) {
                var $elem = $(input);

                if ($elem.hasClass('button'))
                    continue;

                if ($elem.attr('type') === 'hidden')
                    continue;

                $elem.parent().addClass('has-icon has-icon-right');
                $elem.removeClass('is-success is-danger');
                $elem.parent().find('.fa').remove();
                $elem.parent().find('.help').remove();

                if ($elem.attr('name') in errors) {
                    var errorHTML = errors[$elem.attr('name')].join('<br>');
                    $elem.addClass('is-danger');
                    if ($elem.prop('nodeName') !== 'SELECT') {
                        $elem.parent().append('<i class="fa fa-warning"></i>');
                        $elem.parent().append($('<span>').attr('class', 'help is-danger').html(errorHTML));
                    }
                } else {
                    $elem.addClass('is-success');
                    if ($elem.prop('nodeName') !== 'SELECT') {
                        $elem.parent().append('<i class="fa fa-check"></i>');
                    }
                }
            }

            if (data.ok)
                $submitButton.trigger('click', true);
        });

        return false;
    });
};