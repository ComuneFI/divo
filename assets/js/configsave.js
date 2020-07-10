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



$(document).on('click', '.uploadAutonaticConfiguration', function() {
event=$(this).data('evento');
  $('.suggested').each(function(){

    $(this).parent("[name='mapping"+event+"']").val($(this).val()).change();
  })
});



$(document).on('click', '.savebutton', function() {
  Spinner.show();
  var values = $('select').serialize();
  var tabledb = $(this).data('tablename');

  var eventButton=$(this).data('evento');
 
  var nextstate = {};
  var map = {};
  var listIdSource = [];
  var multipleSelection = [];
  var iter = 0;
  var iter_multiple = 0;

  $(".state").each(function() {

    let idEvent = $(this).data('event');

    if(idEvent==eventButton){
  
      let nextStateItem = $(this).val();
      nextstate[idEvent] = nextStateItem;
    }

  });



  $("[name='mapping"+eventButton+"']").each(function() {

    let idDb = $(this).data('iddb');
    let idSource = $(this).val();

    if (listIdSource.indexOf(idSource) != -1 && idSource != '') {
      multipleSelection[iter_multiple] = idSource;
      $(this).parents('.bootstrap-select-wrapper').find('svg').removeClass('d-none')
      iter_multiple++;
    } else {
      $(this).parents('.bootstrap-select-wrapper').find('svg').addClass('d-none')
    }



    map[idDb] = idSource;
    listIdSource[iter] = idSource;
    iter++;



  });


  if (iter_multiple > 0) {
    Spinner.hide();
    Swal.fire('', 'Verificare le selezioni multiple', 'error').then(function() {

    });
    return false;
  }


  $.ajax({
    type: "POST",
    url: '/config/saveconfig',
    data: {
      tabledb: tabledb,
      map: map,
      nextstate: nextstate
    }
  }).done(function(response) {
    Spinner.hide();

    if (response.status == 200) {
      
      Swal.fire({
        text: response.msg,
        icon: 'success',
        confirmButtonText: 'OK'
      }).then((result) => {
        $('#file_csv_filecsv').val('');
        window.location.href=window.location.href;
      })

    } else {
      Spinner.hide();
      Swal.fire('Oops...', response.msg, 'error')

    }
  }).fail(function(jqXHR, textStatus) {
    Spinner.hide();
    Swal.fire('', 'Attenzione si è verificato un errore' + jqXHR, 'error')
  });
});
