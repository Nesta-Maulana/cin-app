$(function () {
    var url = window.location.origin;
    var urlPath = window.location.pathname;
    var path = urlPath.split("/");
    var currentUrl = window.location.href;
    var dashboard = url + "/dashboard";
    var newPath = url;
    if (path.length > 1) {
        for (i = 1; i < 2; i++) {
            newPath += "/";
            newPath += path[i];
        }
    }
    if (currentUrl == dashboard) {
        $("#dashboard").addClass("active");
    } else {
        $("#dashboard").removeClass("active");
    }

    var menuLink = Array.from(document.querySelectorAll("a.menu-link")).filter(
        function (link) {
            return link.href === newPath;
        }
    )[0];

    // Jika ditemukan link yang sesuai, tambahkan kelas 'active' pada elemen <li> terdekat
    if (menuLink) {
        var closestLi = menuLink.closest("li");
        if (closestLi) {
            closestLi.classList.add("active");

            // Tambahkan kelas 'open' pada <li> dengan kelas 'nav-item' terdekat
            var closestNavItem = closestLi.closest("li.nav-item.has-sub");
            if (closestNavItem) {
                closestNavItem.classList.add("open");
            }
        }
    }

    var breadcrumbList = document.createElement("ol");
    breadcrumbList.classList.add("breadcrumb");

    // Tambahkan breadcrumb item untuk setiap elemen dalam path
    for (var i = 1; i < path.length; i++) {
        var breadcrumbItem = document.createElement("li");
        breadcrumbItem.classList.add("breadcrumb-item");

        // Jika ini adalah elemen terakhir dalam path, tambahkan class "active"
        if (i === path.length - 1) {
            breadcrumbItem.classList.add("active");
            breadcrumbItem.textContent = path[i].replace("-", " ");
        } else {
            if (isNaN(path[i] * 1)) {
                console.log(path[i] * 1);
                var breadcrumbLink = document.createElement("a");
                breadcrumbLink.href = url + "/" + path[i]; // Atur link sesuai dengan kebutuhan Anda
                breadcrumbLink.textContent = path[i].replace("-", " ");
                breadcrumbItem.append(breadcrumbLink);
            } else {
                var breadcrumbLink = document.createElement("a");
                const firstInputElement = document.querySelector("input[id*='name']");
                // breadcrumbLink.href = url + "/" + path[i]; // Atur link sesuai dengan kebutuhan Anda
                breadcrumbLink.textContent = firstInputElement.value;
                breadcrumbItem.append(breadcrumbLink);
            }
        }
        if (i === 1) {
            document.getElementById("title-breadcumb").innerHTML = path[
                i
            ].replace("-", " ");
        }
        breadcrumbList.append(breadcrumbItem);
    }

    // Tambahkan breadcrumb list ke dalam dokumen
    $("#breadcumb").append(breadcrumbList);
});
