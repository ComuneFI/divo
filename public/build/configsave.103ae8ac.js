(self.webpackChunk=self.webpackChunk||[]).push([[270],{3422:(e,t,n)=>{"use strict";n(2772),n(9826);var r=n(6455),i=n.n(r),a=(n(6023),n(3014)),o=n(9755);o(document).on("click",".uploadAutonaticConfiguration",(function(){event=o(this).data("evento"),o(".suggested").each((function(){o(this).parent("[name='mapping"+event+"']").val(o(this).val()).change()}))})),o(document).on("click",".savebutton",(function(){a.Z.show();o("select").serialize();var e=o(this).data("tablename"),t=o(this).data("evento"),n={},r={},c=[],s=[],u=0,l=0;if(o(".state").each((function(){var e=o(this).data("event");if(e==t){var r=o(this).val();n[e]=r}})),o("[name='mapping"+t+"']").each((function(){var e=o(this).data("iddb"),t=o(this).val();-1!=c.indexOf(t)&&""!=t?(s[l]=t,o(this).parents(".bootstrap-select-wrapper").find("svg").removeClass("d-none"),l++):o(this).parents(".bootstrap-select-wrapper").find("svg").addClass("d-none"),r[e]=t,c[u]=t,u++})),l>0)return a.Z.hide(),i().fire("","Verificare le selezioni multiple","error").then((function(){})),!1;o.ajax({type:"POST",url:"/config/saveconfig",data:{tabledb:e,map:r,nextstate:n}}).done((function(e){a.Z.hide(),200==e.status?i().fire({text:e.msg,icon:"success",confirmButtonText:"OK"}).then((function(e){o("#file_csv_filecsv").val(""),window.location.href=window.location.href})):(a.Z.hide(),i().fire("Oops...",e.msg,"error"))})).fail((function(e,t){a.Z.hide(),i().fire("","Attenzione si è verificato un errore"+e,"error")}))}))},6023:(e,t,n)=>{var r=n(9755);n(285),n(1539),n(8783),n(6992),n(3948),n(9600),r(document).on("click",".csvbutton",(function(){document.querySelector("table").outerHTML;!function(e,t){for(var n=[],r=document.querySelectorAll(".bitable tr"),i=0;i<r.length;i++){for(var a=[],o=r[i].querySelectorAll("td, th"),c=0;c<o.length;c++)a.push(o[c].innerText);n.push(a.join(";"))}!function(e,t){var n,r;n=new Blob([e],{type:"text/csv"}),(r=document.createElement("a")).download=t,r.href=window.URL.createObjectURL(n),r.style.display="none",document.body.appendChild(r),r.click()}(n.join("\n"),t)}(0,r(this).data("filename"))}))},3014:(e,t,n)=>{"use strict";n.d(t,{Z:()=>a});n(9070);var r=n(9755);function i(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,r.key,r)}}const a=function(){function e(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e)}var t,n,a;return t=e,a=[{key:"show",value:function(){var e=document.createElement("div");e.setAttribute("class","loader loader-default is-active"),e.setAttribute("id","bispinnerloader"),document.body.appendChild(e)}},{key:"hide",value:function(){r("#bispinnerloader").remove()}}],(n=null)&&i(t.prototype,n),a&&i(t,a),e}()},2092:(e,t,n)=>{var r=n(9974),i=n(8361),a=n(7908),o=n(7466),c=n(5417),s=[].push,u=function(e){var t=1==e,n=2==e,u=3==e,l=4==e,f=6==e,d=7==e,v=5==e||f;return function(h,p,m,y){for(var b,g,w=a(h),x=i(w),A=r(p,m,3),O=o(x.length),k=0,C=y||c,Z=t?C(h,O):n||d?C(h,0):void 0;O>k;k++)if((v||k in x)&&(g=A(b=x[k],k,w),e))if(t)Z[k]=g;else if(g)switch(e){case 3:return!0;case 5:return b;case 6:return k;case 2:s.call(Z,b)}else switch(e){case 4:return!1;case 7:s.call(Z,b)}return f?-1:u||l?l:Z}};e.exports={forEach:u(0),map:u(1),filter:u(2),some:u(3),every:u(4),find:u(5),findIndex:u(6),filterOut:u(7)}},5417:(e,t,n)=>{var r=n(111),i=n(3157),a=n(5112)("species");e.exports=function(e,t){var n;return i(e)&&("function"!=typeof(n=e.constructor)||n!==Array&&!i(n.prototype)?r(n)&&null===(n=n[a])&&(n=void 0):n=void 0),new(void 0===n?Array:n)(0===t?0:t)}},3157:(e,t,n)=>{var r=n(4326);e.exports=Array.isArray||function(e){return"Array"==r(e)}},9826:(e,t,n)=>{"use strict";var r=n(2109),i=n(2092).find,a=n(1223),o="find",c=!0;o in[]&&Array(1).find((function(){c=!1})),r({target:"Array",proto:!0,forced:c},{find:function(e){return i(this,e,arguments.length>1?arguments[1]:void 0)}}),a(o)},2772:(e,t,n)=>{"use strict";var r=n(2109),i=n(1318).indexOf,a=n(9341),o=[].indexOf,c=!!o&&1/[1].indexOf(1,-0)<0,s=a("indexOf");r({target:"Array",proto:!0,forced:c||!s},{indexOf:function(e){return c?o.apply(this,arguments)||0:i(this,e,arguments.length>1?arguments[1]:void 0)}})}},e=>{"use strict";e.O(void 0,[755,455,893,571],(()=>{return t=3422,e(e.s=t);var t}))}]);