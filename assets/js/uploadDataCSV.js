import Spinner from '../../vendor/comunedifirenze/bicorebundle/assets/js/spinner/waitpage.js';
import './htmlfunction/tablefunction.js';
import Swal from 'sweetalert2';
$( document ).ready(function() {
    Spinner.hide();
   
  

});

$(document).on('click', '.showSpinner', function () {
 
    Spinner.show();

});


$(document).on('change', '.sez_sel', function () {

    let sezione_selezinata=$(this).val();
    let a = $(this).parents('.card-body').find('.read-more');
    if(sezione_selezinata!=''){
        a.addClass('showSpinner');
        let url=a.data('routing');
        let path= ""+url+"/"+sezione_selezinata+"" ;
        a.attr('href',path);
    }else{
        a.removeClass('showSpinner');
        a.attr('href','#');
    }
});

$(document).on('click', '.checkbox', function () {
  if($(this).is(":checked")){
   
    $(this).parents('.tr').find('.inputSave').attr('disabled',false);
  }else{
   
    $(this).parents('.tr').find('.inputSave').attr('disabled',true);
  }
   
});

$(document).on('click', '.save-inputdata', function () {
  Spinner.show();
  var array = {};
    $('.inputSave:not(:disabled)').each(function(){
      let val=$(this).val()
      let iter= $(this).data('iter');
      let name= $(this).attr('name');
      if(array[iter] === undefined){
          array[iter]={};
      }
      array[iter][name]=val;
    })
  

    let tabledb=$(this).data('table');
    $.ajax({
        type: "POST",
        url: '/savesourcedate',
        data: {
          tabledb: tabledb,
          inputdata: array
        }
      }).done(function(response) {
        Spinner.hide();
        if (response.status == 200) {
        
          Swal.fire({
            text: response.msg,
            icon: 'success',
            confirmButtonText: 'OK'
          }).then((result) => {
            Spinner.show();
            window.location.href=window.location.href;
          })

        } else {
        
          Swal.fire('Oops...', response.msg, 'error')

        }
      }).fail(function(jqXHR, textStatus) {
        Spinner.hide();
        Swal.fire('', 'Attenzione si è verificato un errore' + jqXHR, 'error')
       
      });
    });
