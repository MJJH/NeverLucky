/* global $ */
$(function() {
    if (window.location.pathname !== '/') {
        $("a[href*='" + window.location.pathname + "']").addClass("active");
    } else {
        $(".navbar-header a.navbar-brand").addClass("active");
    }
});