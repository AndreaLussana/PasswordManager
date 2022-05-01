//import * as cr from './crypt.js'
var host = "localhost/PasswordManager/";
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
async function Reg(){
    if(Validate()){
        var email = document.getElementById("Remail").value;
        var masterp = document.getElementById("Rpassword").value;
        var settings = {
            "url": "http://" + host + "Backend/api/user.php",
            "method": "PUT",
            "timeout": 0,
            "headers": {
              "Content-Type": "application/json"
            },
            "data": JSON.stringify({
              "email": email,
              "pwd": masterp
            }),
          };
          
          $.ajax(settings).done(function (response) {
            console.log(response);
          });
        
    }
}
function Log(){
    var email = document.getElementById("email").value;
    var masterp = document.getElementById("password").value;
    var settings = {
        "url": "http://" + host + "Backend/api/user.php",
        "method": "POST",
        "timeout": 0,
        "headers": {
            "Content-Type": "application/json"
        },
        "data": JSON.stringify({
            "email": email,
            "pwd": masterp
        }),
        };
          
    $.ajax(settings).done(function (response) {
        console.log(response);
    });

}
document.querySelector("#r").addEventListener('click', ChangeR);
document.querySelector("#l").addEventListener('click', ChangeL);
document.querySelector("#Reg").addEventListener('click', Reg);
document.querySelector("#Log").addEventListener('click', Log);
