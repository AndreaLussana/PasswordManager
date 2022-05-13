var elements = [];
var open = 0;   //1=aggiungi elemento, 2=dettagli elemento
async function checkPassword(){
    var find = false;
    var pass = document.getElementById("input_password").value;
    if(pass == "" || pass==null){
        pass= document.getElementById("detail_password").value;
    }
    var hashed = await digestMessage(pass);
    const hashapi = hashed.slice(0, 5);
    var settings = {
        "url": "https://api.pwnedpasswords.com/range/" + hashapi,
        "method": "GET",
        "timeout": 0
    };
    $.ajax(settings).done(function (response) {
        var ar = response.split("\n");
        for(var i=0;i<ar.length;i++){
            if(ar[i].split(":")[0].toLowerCase().indexOf(hashed.slice(5))>-1){
                find=true;
            }
        }
       if(find == true){
            document.getElementById("messages").innerHTML = "<div class=\"alert alert-danger\" role=\"alert\"><strong>Attenzione! <br></strong>La tua password risulta compromessa</div>";
            setTimeout(function () {$(".alert").alert('close')}, 3000);
       }else{
            document.getElementById("messages").innerHTML = "<div class=\"alert alert-success\" role=\"alert\"><strong>OK! <br></strong>La tua password <strong>NON</strong> risulta compromessa</div>";
            setTimeout(function () {$(".alert").alert('close')}, 3000);
        }
    });
}
async function digestMessage(message) {
    const msgUint8 = new TextEncoder().encode(message);                           
    const hashBuffer = await crypto.subtle.digest('SHA-1', msgUint8);           
    const hashArray = Array.from(new Uint8Array(hashBuffer));                     
    const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join(''); 
    return hashHex;
}
function visualpass(){
    var x = document.getElementById("input_password");
    if (x.type === "password") {
        x.type = "text";
        document.getElementById("visualpass").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16"><path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/></svg>';
    } else {
        x.type = "password";
        document.getElementById("visualpass").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/></svg>';
    }
}
function generapass(){
    $('#genpassmodal').modal('show');
    if($('#elementdetail').is(':visible')){
        open = 2;
        $('#elementdetail').modal('hide');
    }else{
        open = 1;
        $('#exampleModalCenter').modal('hide');
    }
    genpass();
    var l = document.getElementById("passlength").value;
    document.getElementById("lennum").innerHTML = l;
}
function genpassclose(){
    $('#genpassmodal').modal('hide');
    if(open==1){
        $('#exampleModalCenter').modal('show');
    }else if(open==2){
        $('#elementdetail').modal('show');
    }
    
}
function genpasssave(){
    $('#genpassmodal').modal('hide');
    var pass = document.getElementById("genpassbox").value;
    if(open==1){
        $('#exampleModalCenter').modal('show');
        document.getElementById("input_password").value = pass;
    }else if(open==2){
        $('#elementdetail').modal('show');
        document.getElementById("detail_password").value = pass;
    }
}
function rigenpass(){
    genpass();
}
function copiapass(){
    var copyText = document.getElementById("genpassbox");
    copyText.select(); 
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    document.getElementById("messages").innerHTML = "<div class=\"alert alert-success\" role=\"alert\"><strong>Copiato!</strong></div>";
    setTimeout(function () {$(".alert").alert('close')}, 3000);
}
function genpass(){
    var up = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"; var low = "abcdefghijklmnopqrstuvwxyz"; var num="0123456789"; var spec="!@#$%^&*";
    var final = ""; var password="";
    var len = document.getElementById("passlength").value;
    if($("#uppercase").is(":checked")){
        final+=up;
    }
    if($("#lowercase").is(":checked")){
        final+=low;
    }
    if($("#number").is(":checked")){
        final+=num;
    }
    if($("#special").is(":checked")){
        final+=spec;
    }
    for (var i = 0; i <= len; i++) {
        var randomNumber = Math.floor(Math.random() * final.length);
        password += final.substring(randomNumber, randomNumber +1);
    }
    document.getElementById("genpassbox").value = password;
}
document.querySelector("#integr").addEventListener('click', checkPassword);
document.querySelector("#detail_integr").addEventListener('click', checkPassword);
document.querySelector("#visualpass").addEventListener('click', visualpass);
document.querySelector("#detail_visualpass").addEventListener('click', function(){
    var x = document.getElementById("detail_password");
    if (x.type === "password") {
        x.type = "text";
        document.getElementById("detail_visualpass").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16"><path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/><path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/><path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/></svg>';
    } else {
        x.type = "password";
        document.getElementById("detail_visualpass").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/></svg>';
    }
});
document.querySelector("#generapass").addEventListener('click', generapass);
document.querySelector("#detail_generapass").addEventListener('click', generapass);
document.querySelector("#genpassclose").addEventListener('click', genpassclose);
document.querySelector("#genpasssave").addEventListener('click', genpasssave);
document.querySelector("#rigenpass").addEventListener('click', rigenpass);
document.querySelector("#copiapass").addEventListener('click', copiapass);
document.querySelector("#uppercase").addEventListener('click', function(){
    var $boxes = $('input[name=passch]:checked');
    if($boxes.length<1){
        $('#uppercase').prop('checked', true);
    }
    genpass();
});
document.querySelector("#lowercase").addEventListener('click', function(){
    var $boxes = $('input[name=passch]:checked');
    if($boxes.length<1){
        $('#lowercase').prop('checked', true);
    }
    genpass();
});
document.querySelector("#number").addEventListener('click', function(){
    var $boxes = $('input[name=passch]:checked');
    if($boxes.length<1){
        $('#number').prop('checked', true);
    }
    genpass();
});
document.querySelector("#special").addEventListener('click', function(){
    var $boxes = $('input[name=passch]:checked');
    if($boxes.length<1){
        $('#special').prop('checked', true);
    }
    genpass();
});
$('#genpassmodal').on('hidden.bs.modal', function (e) {
    if(open==1){
        $('#exampleModalCenter').modal('show');
    }else if(open==2){
        $('#elementdetail').modal('show');
    }
    
})
$('#passlength').on("click", function() {
    var l = document.getElementById("passlength").value;
    document.getElementById("lennum").innerHTML = l;
    genpass();
});
document.querySelector("#addfav").addEventListener('click', function(){
    if($("#addfav").hasClass("filled")){
        document.getElementById("addfav").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-star" viewBox="0 0 16 16"><path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288L8 2.223l1.847 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.565.565 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z"/></svg>';
        $("#addfav").removeClass("filled");
    }else{
        document.getElementById("addfav").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg>';
        $("#addfav").addClass("filled");
    }
});
document.querySelector("#home_btn").addEventListener('click', function(){
    $('#Home').removeClass("d-none");
    $('#Preferiti').addClass("d-none");
    $('#Team').addClass("d-none");
    $('#Impostazioni').addClass("d-none");
});
document.querySelector("#preferiti_btn").addEventListener('click', function(){
    $('#Home').addClass("d-none");
    $('#Preferiti').removeClass("d-none");
    $('#Team').addClass("d-none");
    $('#Impostazioni').addClass("d-none");
});
document.querySelector("#team_btn").addEventListener('click', function(){
    $('#Home').addClass("d-none");
    $('#Preferiti').addClass("d-none");
    $('#Team').removeClass("d-none");
    $('#Impostazioni').addClass("d-none");
});
document.querySelector("#impostazioni_btn").addEventListener('click', function(){
    $('#Home').addClass("d-none");
    $('#Preferiti').addClass("d-none");
    $('#Team').addClass("d-none");
    $('#Impostazioni').removeClass("d-none");
});
document.querySelector("#logout_btn").addEventListener('click', function(){
    sessionStorage.removeItem("key");
    sessionStorage.removeItem("jwt");
    post('home.php', {type: 'logout'});
});
function post(path, params, method='post') {
    const form = document.createElement('form');
    form.method = method;
    form.action = path;
  
    for (const key in params) {
      if (params.hasOwnProperty(key)) {
        const hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.name = key;
        hiddenField.value = params[key];
  
        form.appendChild(hiddenField);
      }
    }
  
    document.body.appendChild(form);
    form.submit();
  }
  
document.querySelector("#save_element").addEventListener('click', function(){
    var fav = false;
    if($("#addfav").hasClass("filled")){
        fav=true;
    }
    var new_el = new elemento($('#input_nome').val(), $('#input_username').val(), $('#input_email').val(), $('#input_password').val(), fav, $('#input_url').val(), $('#input_note').val());
    add_element(new_el);
    $('#exampleModalCenter').modal('toggle');
});
function add_element(el){
    var str = JSON.stringify(el);
    var encrypted = CryptoJS.AES.encrypt(str, sessionStorage.getItem('key'));
    //Invio l'oggetto alle api per salvarlo sul database
    var settings = {
        "url": "../../../Backend/api/element.php",
        "method": "PUT",
        "timeout": 0,
        "headers": {
            "Content-Type": "application/json",
            "Authorization": "Bearer "+ sessionStorage.getItem('jwt')
        },
        "data": JSON.stringify({
            "ceu": encrypted.toString()
        }),
        };
          
    $.ajax(settings).done(function (response) {
        $('#Home').empty();
        req_elem();
    });
}
$( document ).ready(function() {    //Funzione quando ricarico la pagina
   req_elem();
});
function req_elem(){
    elements=[];
    //Richiesta degli oggetti ////////////////////////////////////////////////////////////////// modifica con il jwt
    var settings = {
        "url": "../../../Backend/api/element.php/all",
        "method": "GET",
        "timeout": 0,
        "headers": {
            "Content-Type": "application/json",
            "Authorization": "Bearer "+ sessionStorage.getItem('jwt')
        }
        };
          
    $.ajax(settings).done(function (response) {
        var t = response.response;
        var spl = t.split(":");
        for(var i=0;i<spl.length;i++){
            if(spl[i]!=""){
                //in e[0] é presente l'id dell'elemento
                var e = spl[i].split("?");
                var decrypted = CryptoJS.AES.decrypt(e[1], sessionStorage.getItem('key'));
                var pu = JSON.parse(decrypted.toString(CryptoJS.enc.Utf8));
                pu.id = e[0];
                elements.push(pu);
            }
        }
        var pri = '<h1 class="mb-5">Home</h1>';
        var pre = '<h1 class="mb-5">Preferiti</h1>';
        for(var i=0;i<elements.length;i++){
            pri+='<div class="row shadow p-2 mb-1 bg-white unselectable row_el" id="'+i+','+elements[i].id+'"><div class="col-sm">'+elements[i].name+'</div><div class="col-sm">'+elements[i].email+'</div><div class="col-sm" id="col_last">'+elements[i].url+'</div></div>';
            if(elements[i].favourite == true){
                pre+='<div class="row shadow p-2 mb-1 bg-white unselectable row_el" id="'+i+','+elements[i].id+'"><div class="col-sm">'+elements[i].name+'</div><div class="col-sm">'+elements[i].email+'</div><div class="col-sm" id="col_last">'+elements[i].url+'</div></div>';;
            }

        }
        if(elements.length>0){
            document.getElementById("Home").innerHTML = pri;
            document.getElementById("Preferiti").innerHTML = pre;
        }
    });
}
$('#exampleModalCenter').on('hidden.bs.modal', function () {
    if(!$('#genpassmodal').is(':visible')){
        $('#exampleModalCenter').find("input[type=text],input[type=email], input[type=password], textarea").val("");
    }
});
$(document).on('click','.row_el',function(){
    var sp = (this.id).split(",");
    var index = sp[0];
    var id = sp[1];
    $('#detail_nome').val(elements[index].name);
    $('#detail_username').val(elements[index].username);
    $('#detail_email').val(elements[index].email);
    $('#detail_password').val(elements[index].password);
    //Gestire il check della password e la generazione (cambiare il take del value nella funzione che viene richiamata quando premo sul bottoine)
    $('#detail_url').val(elements[index].url);
    if(elements[index].favourite == true){
        document.getElementById("detailfav").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg>';
        $("#detailfav").addClass("filled");
    }
    $('#detail_note').val(elements[index].note);
    $('#elementdetail').modal('show');
});
$('#elementdetail').on('hidden.bs.modal', function () {
    if(!$('#genpassmodal').is(':visible')){
        $('#elementdetail').find("input[type=text],input[type=email], input[type=password], textarea").val("");
        document.getElementById("detailfav").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-star" viewBox="0 0 16 16"><path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288L8 2.223l1.847 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.565.565 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z"/></svg>';
        $("#addfav").removeClass("detailfav");
    }
});
document.querySelector("#detailsclose").addEventListener('click', function(){
    $('#elementdetail').modal('hide');
});
document.querySelector("#detailssave").addEventListener('click', function(){
    //Richiedi conferma di modificare l'elemento
    //Modifica elemento salvando prima sul database, poi in array elements e infine aggiorna gli elementi mostrati nella pagina
});
document.querySelector("#create_team").addEventListener('click', function(){
    //Possibilità di creare un solo team ma puó partecipare a piú team
});
document.querySelector("#addel_team").addEventListener('click', function(){
    //Aggiungi elemento ad un team e inserisci la cartella dove selezionare il team (da inserire poi nel modifica totale)
});