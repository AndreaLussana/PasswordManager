<?php
session_start();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = $_POST["mail"];
    $pwd = $_POST["pass"];
    $t = $_POST["req"];
    if($t=="login"){
      $postdata = [
        "email"=>$email,
        "pwd"=>$pwd
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
        CURLOPT_POSTFIELDS =>json_encode($postdata),
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json'
        ),
      ));
      $response = curl_exec($curl);
      $r = json_decode($response, true);
      $spl = explode(":", $r["response"]);
      $_SESSION["key"]= utf8_decode($spl[0]);
      $_SESSION["id"] = $spl[1];
      header("Location: home.php");
      exit();
    }elseif($t=="reg"){
      //Fai registrazione
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Form</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous"/>
        <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
        <script type="module" src="Script.js"></script>
        <script src="utf8.js"></script>
    </head>
        <body>
      <div class="container d-flex justify-content-center align-items-center" style="margin-top: 15%;">
        <form method="post" action="" style="display: block;" id="LoginForm">
            <h1 style="display: inline;">Login<div style="display:inline;color:darkgrey;cursor:pointer;" id="r"> Registrazione</div></h1>
            <div class="form-group">
              <label for="exampleInputEmail1">Email address</label>
              <input type="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Enter email" name="mail">
              <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>
            <div class="form-group">
              <label for="exampleInputPassword1">Password</label>
              <input type="password" class="form-control" id="password" placeholder="Password" name="pass">
            </div>
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="exampleCheck1" name="Ricordami">
              <label class="form-check-label" for="exampleCheck1">Resta Connesso</label>
            </div>
            <input type="hidden" name="req" value="login"></input>
            <button type="submit" class="btn btn-primary" style="margin-top: 2%;" id="Log">Submit</button>
          </form>

          <form method="post" action="" style="display: none;" id="RegForm">
            <h1 style="display: inline;">Registrazione<div style="display:inline;color:darkgrey;cursor:pointer;" id="l"> Login</div></h1>
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
              <input type="checkbox" class="form-check-input" id="exampleCheck2" name="Ricordami">
              <label class="form-check-label" for="exampleCheck1">Resta Connesso</label>
            </div>
            <div class="row">
            <div class="col-5">
            <input type="hidden" name="req" value="reg"></input>
            <button type="submit" class="btn btn-primary" style="margin-top: 2%;" id="Reg">Submit</button>
            </div>
            <div class="col-5"> 
              <a class="btn btn-lg btn-google btn-block text-uppercase btn-outline border border-secondary" href="https://id.paleo.bg.it/oauth/authorize?client_id=<?php echo $client_id ?>&response_type=code&state=andrea&redirect_uri=<?php echo $redirect ?>"><img src="https://img.icons8.com/color/16/000000/google-logo.png"></a> 
            </div></div>
            </form><br>
      </div>
    </body>
</html>