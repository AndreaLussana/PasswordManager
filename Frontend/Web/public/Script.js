function Validate() {
    var password = document.getElementById("Rpassword").value;
    var confirmPassword = document.getElementById("Rconfirm_password").value;
    if (password != confirmPassword) {
        alert("Passwords do not match.");
        return false;
    }
    return true;
}
function ChangeR(){
        document.getElementById("LoginForm").style.display = "none";
        document.getElementById("RegForm").style.display = "block";
}
function ChangeL(){
    document.getElementById("LoginForm").style.display = "block";
    document.getElementById("RegForm").style.display = "none";
}
$( document ).ready(function() {
    if(localStorage.getItem("cookie") === null){
        localStorage.setItem("cookie", "false");
    }
});
document.querySelector("#r").addEventListener('click', ChangeR);
document.querySelector("#l").addEventListener('click', ChangeL);
document.querySelector("#Lricordami").addEventListener('click', function(){
    if(localStorage.getItem("cookie") != "true"){
        $('#modalCookie1').modal('show');
    }
});
document.querySelector("#Rricordami").addEventListener('click', function(){
    if(localStorage.getItem("cookie") != "true"){
        $('#modalCookie1').modal('show');
    }
});
document.querySelector("#acceptcookie").addEventListener('click', function(){
    localStorage.setItem("cookie", "true");
    $('#modalCookie1').modal('hide');
});
document.querySelector("#rejectcookie").addEventListener('click', function(){
    $('#modalCookie1').modal('hide');
});
$('#modalCookie1').on('hidden.bs.modal', function () {
    if(localStorage.getItem("cookie") != "true"){
        $( "#Rricordami, #Lricordami" ).prop( "checked", false );
    }
});
document.querySelector("#Reg").addEventListener('click', function(){
    if(Validate()){
        $("RegForm").submit();
    }
});
