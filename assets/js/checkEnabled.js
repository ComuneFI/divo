/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
//require('../css/app.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');
import Swal from 'sweetalert2';
var checkbutton=false;
$(document).ready(function(){
    $('.checkEnabled').each(function(){
        let nextstate=$(this).data('nextstate');
        let entityref= $(this).data('entityref');
        let button= $(this);
        
        if(checkbutton){
            $.ajax({
                type: "POST",
                url: '/checkEnabled',
                data: {nextstate: nextstate, entityref: entityref}
            }).done(function (response) {
            
            
                if(response){
                    button.removeClass('disabled');
                    button.attr('aria-disabled','false');
        
                }else{
                    button.parents('a').attr('href','#')
                }
         
         
          
            }).fail(function (jqXHR, textStatus) {

                Swal.fire('', 'Attenzione si è verificato un errore' + jqXHR, 'error')
            }); 
        }else{
            button.removeClass('disabled');
            button.attr('aria-disabled','false');
        }
    })
  
});