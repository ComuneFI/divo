/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)


// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');
// require('../css/config.css');
import Swal from 'sweetalert2';
import './htmlfunction/tablefunction.js';
import Spinner from '../../vendor/comunedifirenze/bicorebundle/assets/js/spinner/waitpage.js';
$(document).on('click', '#creautente', function () {
    Spinner.show();
    var username = $('#username').val();
    var password = $('#password').val();
    var email = $('#email').val();
    var rtusername = $('#rtusername').val();
    var rtpassword = $('#rtpassword').val();
    console.log(username);
    console.log(password);
    console.log(email);
    console.log(rtusername);
    console.log(rtpassword);

    $.ajax({
        type: "POST",
        url: '/config/createuser',
        data: {
            username: username,
            password: password,
            email: email,
            rtusername: rtusername,
            rtpassword: rtpassword,
        }
    }).done(function (response) {
        Spinner.hide();
        if (response.status == 200) {

            Swal.fire({
                text: response.msg,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
                
                window.location.href = window.location.href;
            })

        } else {
            Spinner.hide();
            Swal.fire('Oops...', response.msg, 'error')

        }
    }).fail(function (jqXHR, textStatus) {
        Spinner.hide();
        Swal.fire('', 'Attenzione si è verificato un errore' + jqXHR, 'error')
    });
});


