window.onload = function() {
    startPage();
    
    window.addEventListener('popstate', function( e ) {
        var xhr = new XMLHttpRequest();
        
        var href = e.target.location.href + ( ( e.target.location.href.indexOf('?') === -1 ) ? '?' : '&' ) + 'format=json';
        xhr.open("GET", href, false);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.send();
        
        if (xhr.status != 200) {
            printErr( 'Сервер временно недоступен, повторите позже' );
        } else {
            result = JSON.parse( xhr.responseText );
            if ( result.backpath !== undefined ) {
                setBackButton( result.backpath );
            }
            if ( result.content !== undefined ) {
                newMainBlock( result.content );
                document.title = result.title + ' — Учётная запись КиберПро.РФ';
                startPage();
            } else {
                printErr( 'Неизвесная ошибка, повторите позже' );
            }
        }
    }, false);
};

function startPage() {
    var elements = document.querySelectorAll('.login__input > input');
    for (var i = 0; i < elements.length; i++) {
        if ( elements[i].value != '' ) {
            elements[i].parentElement.classList.add('login__input_focused');
        }
        elements[i].onchange = function( e ) {
            if ( e.target.value == '' ) {
                e.target.parentElement.classList.remove('login__input_focused');
            } else {
                e.target.parentElement.classList.add('login__input_focused');
            }
        };
    }
    
    var elements = document.querySelectorAll('.login__input__eye');
    for (var i = 0; i < elements.length; i++) {
        elements[i].onclick = function( e ) {
            if ( e.target.classList.contains( 'active' ) ) {
                e.target.classList.remove('active');
                e.target.parentElement.querySelector('input').type = 'password';
            } else {
                e.target.classList.add('active');
                e.target.parentElement.querySelector('input').type = 'text';
            }
        };
    }
    
    if ( window.history !== undefined && history.pushState !== undefined ) {
        switch ( document.location.pathname ) {
            case '/login':
                startLogin();
                break;
            case '/register':
                startRegister();
                break;
        }
    }
}

function startLogin() {
    var loginButton = document.getElementById('login_button');
    loginButton.onclick = function( e ) {
        e.preventDefault();
        
        if ( 
            document.getElementById('input-login').value === '' ||
            document.getElementById('input-pass').value  === ''
        ) {
            return;
        }
        
        var currentMain = document.querySelector('form');
        currentMain.style.pointerEvents = 'none';
        currentMain.style.opacity = '.6';
        
        var xhr = new XMLHttpRequest();

        var body = 'login=' + encodeURIComponent(document.getElementById('input-login').value) +
          '&pass=' + encodeURIComponent(document.getElementById('input-pass').value);

        var href = window.location.href + ( ( window.location.href.indexOf('?') === -1 ) ? '?' : '&' ) + 'format=json';
        xhr.open("POST", href, false);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.send(body);
        
        if (xhr.status != 200) {
            printErr( 'Сервер временно недоступен, повторите позже' );
            
            currentMain.style.cssText = '';
        } else {
            result = JSON.parse( xhr.responseText );
            if ( result.backpath !== undefined ) {
                setBackButton( result.backpath );
            }
            if ( result.status !== undefined ) {
                if ( result.status === 'ok' ) {
                    window.location = result.retpath;
                } else if ( result.status === 'err' ) {
                    printErr( result.err );

                    currentMain.style.cssText = '';
                } else {
                    printErr( 'Неизвесная ошибка, повторите позже' );

                    currentMain.style.cssText = '';
                }
            } else {
                printErr( 'Неизвесная ошибка, повторите позже' );

                currentMain.style.cssText = '';
            }
        }
    }

    addDynamicLink( document.getElementById('register_link') );
    addDynamicLink( document.getElementById('foggot_password') );
}

function startRegister() {
    var passInput1 = document.getElementById('input_pass1');
    var passInput2 = document.getElementById('input_pass2');
    
    var checkPass = function() {
        if ( document.getElementById('input_pass1').value != '' && document.getElementById('input_pass2').value != '' ) {
            if ( document.getElementById('input_pass1').value != document.getElementById('input_pass2').value) {
                document.getElementById('input_pass1').parentNode.classList.add('wrong__input');
                document.getElementById('input_pass2').parentNode.classList.add('wrong__input');
                document.getElementById('input_pass2').parentNode.querySelector('.login__input__message').style.display = 'block';
                document.getElementById('input_pass2').parentNode.querySelector('.login__input__message').innerText = 'Пароли не совпадают!';
                
                return false;
            } else {
                document.getElementById('input_pass1').parentNode.classList.remove('wrong__input');
                document.getElementById('input_pass2').parentNode.classList.remove('wrong__input');
                document.getElementById('input_pass2').parentNode.querySelector('.login__input__message').style.display = 'none';
                
                return true;
            }
        } else {
            document.getElementById('input_pass1').parentNode.classList.remove('wrong__input');
            document.getElementById('input_pass2').parentNode.classList.remove('wrong__input');
            document.getElementById('input_pass2').parentNode.querySelector('.login__input__message').style.display = 'none';
            
            return false;
        }
    };
    
    var loginInput = document.getElementById('input_login');
    loginInput.oninput = function( e ) {
        if ( e.target.value.search(/^[0-9A-Za-z_-]{4,32}$/) === -1 ) {
            e.target.parentNode.classList.add('wrong__input');
            e.target.parentNode.querySelector('.login__input__message').style.display = 'block';
            e.target.parentNode.querySelector('.login__input__message').innerText = 'Логин может содержать от 4 до 32 латинскиих символов, цифр и знаков _ и -';
            return;
        } else {
            e.target.parentNode.querySelector('.login__input__message').style.display = 'none';
            e.target.parentNode.classList.remove('wrong__input');
        }
        
        var xhr = new XMLHttpRequest();
        
        var href = '/api/loginAvailable?login=' + document.getElementById('input_login').value;
        xhr.open("GET", href, false);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.send();
        if (xhr.status == 200) {
            result = JSON.parse( xhr.responseText );
            
            if ( result.available == true ) {
                e.target.parentNode.classList.remove('wrong__input');
                e.target.parentNode.querySelector('.login__input__message').style.display = 'none';
            } else {
                e.target.parentNode.classList.add('wrong__input');
                e.target.parentNode.querySelector('.login__input__message').style.display = 'block';
                e.target.parentNode.querySelector('.login__input__message').innerText = 'К сожалению, логин занят';
            }
        }
    };
    
    passInput1.oninput = checkPass;
    passInput2.oninput = checkPass;
    
    var registerButton = document.getElementById('register_button');
    registerButton.onclick = function( e ) {
        e.preventDefault();
        
        var currentMain = document.querySelector('form');
        currentMain.style.pointerEvents = 'none';
        currentMain.style.opacity = '.6';
        
        var xhr = new XMLHttpRequest();

        var body = 'login=' + encodeURIComponent(document.getElementById('input_login').value) +
                   '&pass1=' + encodeURIComponent(document.getElementById('input_pass1').value) +
                   '&pass2=' + encodeURIComponent(document.getElementById('input_pass2').value);

        var href = window.location.href + ( ( window.location.href.indexOf('?') === -1 ) ? '?' : '&' ) + 'format=json';
        xhr.open("POST", href, false);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.send(body);
        
        if (xhr.status != 200) {
            printErr( 'Сервер временно недоступен, повторите позже' );
            
            currentMain.style.cssText = '';
        } else {
            result = JSON.parse( xhr.responseText );
            if ( result.backpath !== undefined ) {
                setBackButton( result.backpath );
            }
            if ( result.status === 'ok' ) {
                window.location = result.retpath;
            } else if ( result.status === 'auth' ) {
                window.location = result.retpath;
            } else if ( result.status === 'err' ) {
                printErr( result.err );
            
                if ( result.login === undefined ) {
                    document.getElementById('input_login').parentNode.classList.add('wrong__input');
                } else {
                    document.getElementById('input_login').parentNode.classList.remove('wrong__input');
                }
            
                currentMain.style.cssText = '';
            } else {
                printErr( 'Неизвесная ошибка, повторите позже' );
                
                currentMain.style.cssText = '';
            }
        }
    }
    
    addDynamicLink( document.getElementById('login_link') );
}

function addDynamicLink( object ) {
    object.onclick = function( e ) {
        e.preventDefault();
        
        var xhr = new XMLHttpRequest();
        
        var href = e.target.href + ( ( e.target.href.indexOf('?') === -1 ) ? '?' : '&' ) + 'format=json';
        xhr.open("GET", href, false);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.send();
        
        if (xhr.status != 200) {
            printErr( 'Сервер временно недоступен, повторите позже' );
        } else {
            result = JSON.parse( xhr.responseText );
            if ( result.backpath !== undefined ) {
                setBackButton( result.backpath );
            }
            if ( result.content !== undefined ) {
                newMainBlock( result.content );
                document.title = result.title;
                history.pushState( {}, result.title, e.target.href );
                startPage();
            } else {
                printErr( 'Неизвесная ошибка, повторите позже' );
            }
        }
    }
}


function setBackButton( url ) {
    if ( document.querySelector('.return__button') !== null ) {
        document.querySelector('.return__button').href = url;
    } else {
        var a = document.createElement('a');
        a.href = url;
        a.className = "return__button";
        a.title = "Вернуться назад";
        document.querySelector('.main').prepend(a);
    }
}

function printErr( text ) {
    if ( document.querySelector('.err-block') !== null ) {
        document.querySelector('.err-block').innerHTML = text;
    } else {
        var div = document.createElement('div');
        div.innerHTML = text;
        div.className = "err-block";
        document.querySelector('.main__block__container').prepend(div);
    }
}

function newMainBlock( html ) {
    // Высота
    var div = document.createElement('div');
    div.className = "main__block__container main__block__container_new";
    div.innerHTML = html;
    document.querySelector('.main__block').style.height = document.querySelector('.main__block').clientHeight + 'px';
    document.querySelector('.main__block').appendChild(div);
    window.setTimeout(function() {
        document.querySelector('.main__block').style.height = div.clientHeight + 'px';;
        div.style.left = '0';
    }, 0);
    window.setTimeout(function() {
        document.querySelector('.main__block').removeChild( document.querySelector('.main__block__container') );
        div.classList.remove('main__block__container_new');
        div.style.left = '';
        document.querySelector('.main__block').style.height = '';
    }, 400);
}