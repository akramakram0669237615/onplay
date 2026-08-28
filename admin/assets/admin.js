/* ==========================================
   ONPLAY ADMIN PANEL
   MOBILE SIDEBAR
========================================== */

document.addEventListener("DOMContentLoaded", function () {

    var sidebar = document.getElementById("side");
    var overlay = document.getElementById("sidebarOverlay");
    var openMenu = document.getElementById("openMenu");
    var closeMenu = document.getElementById("closeMenu");

    /* ==========================================
       OPEN SIDEBAR
    ========================================== */

    function openSidebar() {

        if (!sidebar) {
            return;
        }

        sidebar.classList.add("open");

        if (overlay) {
            overlay.classList.add("show");
        }

        document.body.classList.add("menu-open");
    }


    /* ==========================================
       CLOSE SIDEBAR
    ========================================== */

    function closeSidebar() {

        if (!sidebar) {
            return;
        }

        sidebar.classList.remove("open");

        if (overlay) {
            overlay.classList.remove("show");
        }

        document.body.classList.remove("menu-open");
    }


    /* ==========================================
       MENU BUTTON
    ========================================== */

    if (openMenu) {

        openMenu.addEventListener("click", function (event) {

            event.preventDefault();

            openSidebar();

        });

    }


    /* ==========================================
       CLOSE BUTTON
    ========================================== */

    if (closeMenu) {

        closeMenu.addEventListener("click", function (event) {

            event.preventDefault();

            closeSidebar();

        });

    }


    /* ==========================================
       OVERLAY
    ========================================== */

    if (overlay) {

        overlay.addEventListener("click", function () {

            closeSidebar();

        });

    }


    /* ==========================================
       ESCAPE KEY
    ========================================== */

    document.addEventListener("keydown", function (event) {

        if (event.key === "Escape") {

            closeSidebar();

        }

    });


    /* ==========================================
       CLOSE ON MOBILE NAV CLICK
    ========================================== */

    var navLinks = document.querySelectorAll(
        ".sidebar .nav-link"
    );

    navLinks.forEach(function (link) {

        link.addEventListener("click", function () {

            if (window.innerWidth <= 900) {

                closeSidebar();

            }

        });

    });


    /* ==========================================
       RESET ON DESKTOP
    ========================================== */

    window.addEventListener("resize", function () {

        if (window.innerWidth > 900) {

            closeSidebar();

        }

    });


    /* ==========================================
       DISABLE SPELLCHECK
    ========================================== */

    document.querySelectorAll("textarea").forEach(function (x) {

        x.setAttribute("spellcheck", "false");

    });

});