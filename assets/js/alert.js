/**
 * Registered and event that close each .alert div box when select changes its value.
 * It means that we are going to do a new transmission.
 */
$("#selectOption").change( function () {
    $(".alert").alert('close');
    //console.log("Eccomi dopo evento"); 
 })