<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $request=true;
  $email = $_POST["mail"];
  $pwd = $_POST["pass"];
  if(isset($_POST["Lricordami"])){
    setcookie("email", $email, time() + (10 * 365 * 24 * 60 * 60), "/"); 
  }
  $t = $_POST["req"];
  if ($t == "login") {
    $postdata = [
      "email" => $email,
      "pwd" => $pwd
    ];
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'localhost/passwordmanager/Backend/api/user.php',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => json_encode($postdata),
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
      ),
    ));
    $response = curl_exec($curl);
    $r = json_decode($response, true);
    if($r == null || $r["status"] == false){
      $request=false;
    }else{
      $spl = explode(":", $r["response"]);
      $_SESSION["key"] = utf8_decode($spl[0]);
      $_SESSION["jwt"] = $spl[1];
      header("Location: home.php");
      exit();
    }
  } elseif ($t == "reg") {
    //Fai registrazione
  }
}

?>
<!DOCTYPE html>
<html>

<head>
  <title>Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous" />
  <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
  <script type="module" src="Script.js"></script>
  <style>
    .alert {
    width:20%; 
    float:right;   
    position: absolute;
    top:2%;
    right:2%;
    z-index: 999999;
 }
  </style>
</head>

<body>
  <div id="messages">
    <?php
      if(isset($request) && $request == false){
        echo '<div class="alert alert-danger" role="alert"><strong>Attenzione! <br></strong>Errore nel login</div>';
      }
    ?>
  </div>
  <div class="container d-flex justify-content-center align-items-center" style="margin-top: 15%;">
    <form method="post" action="" style="display: block;" id="LoginForm">
      <h1 style="display: inline;">Login<div style="display:inline;color:darkgrey;cursor:pointer;" id="r"> Registrazione</div>
      </h1>
      <div class="form-group">
        <label for="exampleInputEmail1">Email address</label>
        <input type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email" name="mail" value="<?php if(isset($_COOKIE["email"])){echo $_COOKIE["email"];} ?>">
        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
      </div>
      <div class="form-group">
        <label for="exampleInputPassword1">Password</label>
        <input type="password" class="form-control" id="password" placeholder="Password" name="pass">
      </div>
      <div class="form-check">
        <input type="checkbox" class="form-check-input" id="Lricordami" name="Lricordami">
        <label class="form-check-label" for="exampleCheck1">Ricorda email</label>
      </div>
      <input type="hidden" name="req" value="login"></input>
      <button type="submit" class="btn btn-primary" style="margin-top: 2%;" id="Log">Submit</button>
    </form>

    <form method="post" action="" style="display: none;" id="RegForm">
      <h1 style="display: inline;">Registrazione<div style="display:inline;color:darkgrey;cursor:pointer;" id="l"> Login</div>
      </h1>
      <div class="form-group">
        <label for="exampleInputEmail1">Email address</label>
        <input type="email" class="form-control" id="Remail" aria-describedby="emailHelp" placeholder="Enter email" name="mail">
        <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
      </div>
      <div class="form-group">
        <label for="exampleInputPassword1">Password</label>
        <input type="password" class="form-control" id="Rpassword" placeholder="Password" name="pass" required>
      </div>
      <div class="form-group">
        <label for="exampleInputPassword1">Confirm password</label>
        <input type="password" class="form-control" id="Rconfirm_password" placeholder="Confirm Password" name="confpass" required>
      </div>
      <div class="form-check">
        <input type="checkbox" class="form-check-input" id="Rricordami" name="Rricordami">
        <label class="form-check-label" for="exampleCheck1">Ricorda email</label>
      </div>
      <div class="row">
        <div class="col-5">
          <input type="hidden" name="req" value="reg"></input>
          <button type="submit" class="btn btn-primary" style="margin-top: 2%;" id="Reg">Submit</button>
        </div>
      </div>
    </form><br>
  </div>
  <!--Modal: modalCookie-->
  <div class="modal fade top" id="modalCookie1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="true">
      <div class="modal-dialog modal-frame modal-top modal-notify modal-info" role="document">
        <!--Content-->
        <div class="modal-content">
          <!--Body-->
          <div class="modal-body">
            <div class="row d-flex justify-content-center align-items-center">
              <p class="pt-3 pr-2">Utilizziamo i cookie per ricordarti l'email. <br> Premere "Accetto" per usufruire della funzione</p>
            </div>
            <div class="row d-flex justify-content-center align-items-center">
              <div class="col-5">
                <button type="button" class="btn btn-outline-primary" id="rejectcookie">Rifiuto</button>
              </div>
              <div class="col-5">
                <button type="button" class="btn btn-primary" id="acceptcookie">Accetto</button>
              </div>
            </div>
          </div>
        </div>
        <!--/.Content-->
      </div>
    </div>
    <!--Modal: modalCookie-->
</body>

</html>