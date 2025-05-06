jQuery(document).ready(function ($) {

    function toggle_form() {
        $('.authora-modal').toggleClass('verify');
    }

    function close_modal() {
        $('.authora-modal-container').removeClass('open');
    }

    function open_modal(e) {
        e.preventDefault();
        $('.authora-modal-container').addClass('open');
    }

    $('.authora-close').click(close_modal);

    $('#authora-login').submit(function (e) {

        e.preventDefault();

        let data = $(this).serialize();
        let _this = $(this);
        let _message = $(this).find('.authora-message');
        let _btn = $(this).find('button');
        let _resend = $('.authora-resend');

        $.ajax({
            type: 'POST',
            url: authora.ajax_url,
            data: data,
            beforeSend: function () {
                $(_this).addClass('loading');
                $(_message).removeClass('active');
                $(_btn).attr('disabled', true);
                $(_resend).text('در حال ارسال...').removeClass('active');
            },
            success: function (result) {
                console.log(result);
                if (result.success) {
                    countdown = result.data.duration;
                    set_time(countdown);
                    $(".authora-login-result").text(result.data.message);
                    $('.authora-modal').addClass('verify');
                    $(".authora-codes input").eq(0).focus();

                    $("#authora-verify input[name='mobile']").val(result.data.mobile);
                    $("#authora-verify input[name='_wpnonce']").val(result.data._wpnonce);

                } else {
                    // Handle error case
                    $(_message).addClass('active').find('span').text(result.data.message);
                    console.log('Error: ' + result.data.message);  
                }
            },
            complete: function () {
                $(_this).removeClass('loading');
                $(_btn).attr('disabled', false);
                $(_resend).text('ارسال مجدد');
            },
            error: function (xhr) {
                let result = xhr.responseJSON;
                $(_message).addClass('active').find('span').text(result.data.message);
            },
        });

    });
    
});