/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)


// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');


function changeTime(){
    var stato=$('#selectOptionList').val();
    $('.it-evidence').each(function(){
        $(this).removeClass('it-evidence');
    })
    $('.STATE_'+stato).addClass('it-evidence');
   
}
$(document).on('change', '#selectOptionList', function () {
    changeTime();
});
$(document).ready(function(){
    changeTime();
    console.log('reay');
})