/*
 * This is JS class for multi sending by section for scrutini and preferences
 */

// any CSS you require will output into a single css file (app.css in this case)
//require('../css/app.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');
import Swal from 'sweetalert2';

$(document).ready(function(){
    console.log("[ok] DivoSlicer loaded v.0.1.3");

});

//use a dictionary for labels
var dictionary = {
    error: {
        request: {
            failed: 'Request failed',
            sending: 'Errore durante invio dati',
        }
    },
    title: {
        send: {
            data: 'Dati che verranno inviati: '
        },
        swal: {
            success: 'Operazione conclusa',
            info: 'Informazioni',
            error: 'Errore',
            warning: 'Operazione conclusa con alcune anomalie'
        }
    },
    body: {
        swal: {
            success: 'Operazione conclusa con successo!',
            info: 'Dati da gestire non rilevati',
            error: 'Si è verificato un errore. Controllare la correttezza della richiesta.',
            warning: 'Operazione conclusa con alcune anomalie',
        }
    },
    sections: {
        fine: 'sezioni corrette',
    }
};


// It sends data to the target system. For first takes the list of sections
// that have to manage; then it proceeds to iterate them in order to send data
var sendData = function(path, topic, start_sec, end_sec) {
    var deferred = $.Deferred();
    //it takes list of sections IDs and proceeds with orchestrate
    getVectorOfSections(path, start_sec, end_sec).then( function(arrayVector) {
        orchestrate(path, topic, arrayVector).then (function(result) {
            deferred.resolve(result);
        })
    });
    return deferred.promise();
}

// It look for IDs of sections given a range
// In case of "changed" it will not evaluate start_sec and end_sec
var getVectorOfSections = function(path, start_sec, end_sec) {
    var deferred = $.Deferred();

    $.getJSON( path, {start: start_sec, end: end_sec} )
    .done(function( json ) {
    //console.log( "JSON Data: " + json);
    deferred.resolve(json);
   })
    .fail(function( jqxhr, textStatus, error ) {
    var err = textStatus + ", " + error;
    console.log( dictionary.error.failed + err );
    });
    return deferred.promise();
}

// return the formatted response for Swal types
var getMessage = function(code, message) {
    var jsonResult = {
        esito: {
            esito: {
                codice: code,
                descrizione: message
            }
        }
    };
    return jsonResult;
}

//Defines function responsible to orchestrate AJAX requests.
//This is a DEFERRED function
var orchestrate = function(path, topic, jsonVectorResponse) {
    //console.log("Path is " +  path);
    //console.log("Topic is " + topic );
    var deferred = $.Deferred();
    //how many already elaborated
    var elaboratedNum = 0;
    //check if given period is invalid
    var invalidPeriod = false;
    var invalidEsito = null;
    if (jsonVectorResponse.esito.esito.codice == 405) {
        invalidPeriod = true;
        invalidEsito = jsonVectorResponse.esito;
    }
    //take the vector from vector response
    var arrayVector = jsonVectorResponse.array;
    var arrayLength = arrayVector.length;
    //append some info to the popup window: the size of array to be elaborated
    $("#mynode").html('... '+arrayLength);
   
    var promises = [];
    var htmlError = '<ul>';
    var wellDone = 0;
        $(arrayVector).each( function() {
            var number = this.id;
            promises[number] = $.Deferred();
        });
        //when all promises will be completed:
        $.when.apply($, promises ).done(function(value) {
            htmlError += '</ul>';
            //We take message that will be provided to Swal
            if (invalidPeriod) {
                //user has selected a range not valid
                var jsonResult = getMessage(invalidEsito.esito.codice, invalidEsito.esito.descrizione);
            }
            else if (wellDone == arrayLength) {
                //all elaboration are good
                var appendMessage = ' ('+wellDone +'/' + arrayLength + ' ' + dictionary.sections.fine +')';
                var jsonResult = getMessage(1, dictionary.body.swal.success + appendMessage);
            }
            else {
                var appendMessage = ' ('+wellDone +'/' + arrayLength + ' ' + dictionary.sections.fine +')'+'<br>'+htmlError;
                var jsonResult = getMessage(2, dictionary.body.swal.warning + appendMessage);
            }
            deferred.resolve(jsonResult);
        });
        var pushPath = '/service/post/'+topic;
        // for each item of vector we have to complete a promise
        $(arrayVector).each(function() {
            var secID = this.id;
            var descID = this.desc;
            $.getJSON(pushPath + '/' + secID, function(data) {
                if (data.esito.esito.codice == 1) {
                    wellDone += 1;
                }
                else {
                    htmlError += '<li>'+ descID +' - '+data.esito.esito.descrizione+'</li>';
                }
                promises[secID].resolve(data.result);       
            })
            .fail(function() {                
                    htmlError += '<li>'+ descID +' - ' + dictionary.error.request.sending +'</li>';
                    promises[secID].resolve();
            })
            .always(function() {
                    //anyway I've elaborated
                    elaboratedNum += 1;
                    $("#mynode").html(elaboratedNum+'/'+arrayLength);
            });
        });
    //return deferred promise waiting to be resolved
    return deferred.promise();
} ;


// Function used for "changed" sections and for range of section also
// It opens a Swal popup window and guide the user during the process of data sending
$(".multiSlicer").click( function() {
    //read event id and topic
    let eventid= $(this).data('eventid');
    let topic = $(this).data('topic');
    let topicText = $(this).data('topic-text');
    let path = $(this).data('path');
    //read range where to perform the request
    //they will be used only in case of an interval
    let sec_start = $("#start-sec-"+eventid).val();
    let sec_end = $("#end-sec-"+eventid).val();
    //start guided tour for the user
    Swal.fire({
        title: 'Invio '+topic,
        html: dictionary.title.send.data + topicText + '<br>Invio sezioni <span id="mynode"></span>',
        showCancelButton: true,
        confirmButtonText: 'Procedi',
        showLoaderOnConfirm: true,
        preConfirm: () => { 
            //this is the deferred function executed before to confirm the alert and close it
            return sendData(path, topic, sec_start, sec_end);
        },
        allowOutsideClick: () => !Swal.isLoading()
      }).then((result) => {
          //console.log(result);
        if (result.value) {
            if(typeof result.value.esito == 'undefined') {
                Swal.fire(
                    dictionary.title.swal.error,
                    dictionary.body.swal.error,
                    'error'
                )
            }
            else if (result.value.esito.esito.codice == 1) {
                Swal.fire({
                    title: dictionary.title.swal.success ,
                    html: result.value.esito.esito.descrizione,
                    icon: 'success',
                }                
                ).then(function(){ 
                    location.reload();
                    }
                 )
            }
            else if (result.value.esito.esito.codice == 405) {
                Swal.fire(
                    dictionary.title.swal.info,
                    result.value.esito.esito.descrizione,
                    'info',
                )
            }
            //this is the case of messages with code 2
            else {
                Swal.fire({
                    title: dictionary.title.swal.warning ,
                    html: result.value.esito.esito.descrizione,
                    icon: 'warning',
                }                
                ).then(function(){ 
                    location.reload();
                    }
                 );
            }
        }
        //for all other cases
        else {
            Swal.fire(
                dictionary.title.swal.info,
                dictionary.body.swal.info,
                'info',
            )
        }
      })

});  


//AJAX sending for single section
//TO BE CONTROLLED THAT THIS FUNCTION IS STILL USED WHEN YOU CHECK AFFLUENCES
//IN case use DICTIONARY LABELS
$(".divo-post-data").click( function() {
    //read topic and itemid (communication, section, ...)
    //let eventid= $(this).data('eventid');
    let topic = $(this).data('topic');
    let itemid= $(this).data('itemid');
    //start guided tour for the user
    Swal.fire({
        title: 'Invio Dati',
        text: 'Vuoi procedere con l\'invio dei dati?',
        showCancelButton: true,
        confirmButtonText: 'Procedi',
        showLoaderOnConfirm: true,
        preConfirm: () => {
          return $.ajax({
            type: "POST",
            url: '/service/post/'+topic+'/'+itemid,
        }).fail(function (jqXHR, textStatus) {
            Swal.fire('', 'Attenzione si è verificato un errore:' + jqXHR.responseText, 'error');
        }).catch(function (jqXHR) {
            console.log(jqXHR.responseText);
        })
        },
        allowOutsideClick: () => !Swal.isLoading()
      }).then((result) => {
        if (result.value) {
            if(typeof result.value.esito == 'undefined') {
                Swal.fire(
                    'Errore',
                    'Errore interno generico, contattare l\'amministratore del sistema',
                    'error'
                )
            }
            else if (result.value.esito.esito.codice == 1) {
                Swal.fire({
                    title: 'Invio concluso' ,
                    html: result.value.esito.esito.descrizione,
                    icon: 'success',
                }                
                ).then(function(){ 
                    location.reload();
                    }
                 )
            }
            else {
                Swal.fire(
                    'Dati inseriti non corretti',
                    result.value.esito.esito.descrizione,
                    'info',
                )
            }
        }
        else {
            Swal.fire(
                'Nessun risultato',
                'Chiedere assistenza all\'amministratore del sistema',
                'error',
            )
        }
      })

});  

