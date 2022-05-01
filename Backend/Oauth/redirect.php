<?php
if($_SERVER["REQUEST_METHOD"] == "GET"){
    require_once "../Config/Oauthconf.php";
    require_once "../../class-http-request.php";
    $code = $_GET["code"];
    $state = $_GET["state"];
    //Controlla che lo state sia valido
    $curl = curl_init();

    curl_setopt_array($curl, [
    CURLOPT_URL => "https://id.paleo.bg.it/oauth/token",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "{\n  \"grant_type\": \"authorization_code\",\n  \"code\": \"$code\",\n  \"redirect_uri\": \"$redirect\",\n  \"client_id\": \"$client_id\",\n  \"client_secret\": \"$client_secret\"\n}",
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json"
    ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        $arr = json_decode($response, true);
        /*session_start();
        $_SESSION["token"] = $arr["access_token"];
        setcookie("token", $arr["access_token"], time()+3600, "/", "localhost");*/
        header("Location: ../../index.php");
        exit();
    }
}
?>