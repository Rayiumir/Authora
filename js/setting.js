jQuery(document).ready(function ($) {
    // Toggle SMS settings based on selected driver
    function toggleSmsSettings() {
        var selectedDriver = $('#sms-driver-select').val();
        $('.sms-settings').hide();
        $('#' + selectedDriver + '-settings').show();
    }

    $('#sms-driver-select').on('change', function () {
        toggleSmsSettings();
    });

    // Tab functionality
    $('.authora-tabs .nav-tab').on('click', function (e) {
        e.preventDefault();
        var target = $(this).attr('href');

        // Update active tab
        $('.authora-tabs .nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        // Show target content
        $('.authora-tab-content').removeClass('active');
        $(target).addClass('active');

        // Update URL without page reload
        var newUrl = window.location.href.split('?')[0] + '?page=authora-sms-settings&tab=' + target.replace('#', '');
        window.history.pushState({}, '', newUrl);
    });

    // Initial state
    toggleSmsSettings();
});