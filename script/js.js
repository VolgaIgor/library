window.onload = function () {
    var buttonMenu = document.getElementById('header-menu-button');
    var menu = document.getElementById('header-menu');
    buttonMenu.onclick = function () {
        if ( buttonMenu.classList.contains('active') ) {
            buttonMenu.classList.remove('active');
            menu.classList.remove('active');
        } else {
            buttonMenu.classList.add('active');
            menu.classList.add('active');
        }
    };
};