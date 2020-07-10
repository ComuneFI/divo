
$( document ).ready(function() {
    $('.collapse-body').each(function(){
     
        let num= $(this).find('.record_nuovo').length;
        if(num>0){
            $(this).parents('.collapse-div-open').addClass('show')
        }
    })
});