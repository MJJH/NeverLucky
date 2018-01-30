/* global $ */
"use strict";

$(document).ready(function() {
    $("#register-born").datetimepicker({
        format: 'DD-MM-YYYY',
        locale: 'nl',
        icons: {
            date: 'fa fa-calendar',
            up: 'fa fa-angle-up',
            down: 'fa fa-angle-down',
            previous: 'fa fa-angle-double-left',
            next: 'fa fa-angle-double-right',
            today: 'fa fa-certificate',
            clear: 'fa fa-ban',
            close: 'fa fa-times'
        },
        viewMode: 'years',
        
    });
});