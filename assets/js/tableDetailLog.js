/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */


// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.


$(document).ready(function(){

    $('#selectSez').change(function() {
 
        let selected='sez-'+$(this).val();
        if($(this).val()!='*'){
            $('.tr-sez').hide(function(){
                $('.'+selected).show();
            });
        }else{
            $('.tr-sez').show();
        }
            
        
    })
})
