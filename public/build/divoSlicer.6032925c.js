(self.webpackChunk=self.webpackChunk||[]).push([[465],{2192:(e,i,o)=>{"use strict";var n=o(6455),t=o.n(n),r=o(9755);r(document).ready((function(){console.log("[ok] DivoSlicer loaded v.0.1.3")}));var s={request:{failed:"Request failed",sending:"Errore durante invio dati"}},a={send:{data:"Dati che verranno inviati: "},swal:{success:"Operazione conclusa",info:"Informazioni",error:"Errore",warning:"Operazione conclusa con alcune anomalie"}},c={swal:{success:"Operazione conclusa con successo!",info:"Dati da gestire non rilevati",error:"Si è verificato un errore. Controllare la correttezza della richiesta.",warning:"Operazione conclusa con alcune anomalie"}},l={fine:"sezioni corrette"},u=function(e,i,o){var n=r.Deferred();return r.getJSON(e,{start:i,end:o}).done((function(e){n.resolve(e)})).fail((function(e,i,o){var n=i+", "+o;console.log(s.failed+n)})),n.promise()},d=function(e,i){return{esito:{esito:{codice:e,descrizione:i}}}},f=function(e,i,o){var n=r.Deferred(),t=0,a=!1,u=null;405==o.esito.esito.codice&&(a=!0,u=o.esito);var f=o.array,v=f.length;r("#mynode").html("... "+v);var h=[],m="<ul>",p=0;r(f).each((function(){var e=this.id;h[e]=r.Deferred()})),r.when.apply(r,h).done((function(e){if(m+="</ul>",a)var i=d(u.esito.codice,u.esito.descrizione);else if(p==v){var o=" ("+p+"/"+v+" "+l.fine+")";i=d(1,c.swal.success+o)}else o=" ("+p+"/"+v+" "+l.fine+")<br>"+m,i=d(2,c.swal.warning+o);n.resolve(i)}));var w="/service/post/"+i;return r(f).each((function(){var e=this.id,i=this.desc;r.getJSON(w+"/"+e,(function(o){1==o.esito.esito.codice?p+=1:m+="<li>"+i+" - "+o.esito.esito.descrizione+"</li>",h[e].resolve(o.result)})).fail((function(){m+="<li>"+i+" - "+s.request.sending+"</li>",h[e].resolve()})).always((function(){t+=1,r("#mynode").html(t+"/"+v)}))})),n.promise()};r(".multiSlicer").click((function(){var e=r(this).data("eventid"),i=r(this).data("topic"),o=r(this).data("topic-text"),n=r(this).data("path"),s=r("#start-sec-"+e).val(),l=r("#end-sec-"+e).val();t().fire({title:"Invio "+i,html:a.send.data+o+'<br>Invio sezioni <span id="mynode"></span>',showCancelButton:!0,confirmButtonText:"Procedi",showLoaderOnConfirm:!0,preConfirm:function(){return function(e,i,o,n){var t=r.Deferred();return u(e,o,n).then((function(o){f(e,i,o).then((function(e){t.resolve(e)}))})),t.promise()}(n,i,s,l)},allowOutsideClick:function(){return!t().isLoading()}}).then((function(e){e.value?void 0===e.value.esito?t().fire(a.swal.error,c.swal.error,"error"):1==e.value.esito.esito.codice?t().fire({title:a.swal.success,html:e.value.esito.esito.descrizione,icon:"success"}).then((function(){location.reload()})):405==e.value.esito.esito.codice?t().fire(a.swal.info,e.value.esito.esito.descrizione,"info"):t().fire({title:a.swal.warning,html:e.value.esito.esito.descrizione,icon:"warning"}).then((function(){location.reload()})):t().fire(a.swal.info,c.swal.info,"info")}))})),r(".divo-post-data").click((function(){var e=r(this).data("topic"),i=r(this).data("itemid");t().fire({title:"Invio Dati",text:"Vuoi procedere con l'invio dei dati?",showCancelButton:!0,confirmButtonText:"Procedi",showLoaderOnConfirm:!0,preConfirm:function(){return r.ajax({type:"POST",url:"/service/post/"+e+"/"+i}).fail((function(e,i){t().fire("","Attenzione si è verificato un errore:"+e.responseText,"error")})).catch((function(e){console.log(e.responseText)}))},allowOutsideClick:function(){return!t().isLoading()}}).then((function(e){e.value?void 0===e.value.esito?t().fire("Errore","Errore interno generico, contattare l'amministratore del sistema","error"):1==e.value.esito.esito.codice?t().fire({title:"Invio concluso",html:e.value.esito.esito.descrizione,icon:"success"}).then((function(){location.reload()})):t().fire("Dati inseriti non corretti",e.value.esito.esito.descrizione,"info"):t().fire("Nessun risultato","Chiedere assistenza all'amministratore del sistema","error")}))}))}},e=>{"use strict";e.O(void 0,[755,455],(()=>{return i=2192,e(e.s=i);var i}))}]);