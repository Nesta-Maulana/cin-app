$(function () {
    var url = window.location.origin;
    var urlPath = window.location.pathname;
    var path = urlPath.split("/");
    var currentUrl = window.location.href;
    var dashboard = url + '/admin';

    var newPath = url;
    if (path.length > 1) {
        for (i = 1; i < 3; i++) {
            newPath += "/";
            newPath += path[i];
        }
    }

    if (currentUrl == dashboard) {
        $('#dashboard').addClass('active');
    } else {
        $('#dashboard').removeClass('active');
    }
    
    $('a.menu-link').filter(function () {
        return this.href == newPath;
    }).parents('.menu-item').addClass('active').addClass('open');
});