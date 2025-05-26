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

    // Handle form submission
    $('form').on('submit', function(e) {
        var activeTab = $('.authora-tab-content.active').attr('id');
        $('input[name="active_tab"]').val(activeTab);
        
        // Store the selected driver value
        var selectedDriver = $('#sms-driver-select').val();
        localStorage.setItem('authora_selected_driver', selectedDriver);
    });

    // Restore selected driver on page load
    var savedDriver = localStorage.getItem('authora_selected_driver');
    if (savedDriver) {
        $('#sms-driver-select').val(savedDriver);
        toggleSmsSettings();
    }

    // Initial state
    toggleSmsSettings();
});