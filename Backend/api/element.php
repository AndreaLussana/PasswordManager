<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization");
if($_SERVER["REQUEST_METHOD"] == "POST"){   //Update info about element
    header("Access-Control-Allow-Methods: POST");
    require_once "../Config/DB.php";
    include("crypto/jwt.php");
    $jwt = getBearerToken();
    $id = verify($jwt, $secret_key);
    $data = json_decode(file_get_contents("php://input"), true);
    if($data!=null){
        $ceu = $data["ceu"];    //Elemento
        $id_el = $data["idel"]; //id elemento
    }
    if(empty($ceu)||empty($id_el)){
        $ceu = $_PUT["ceu"];
        $id_el = $_PUT["idel"];
        if(empty($ceu) || empty($id) || empty($id_el)){
            response("Error sending data", false, "");
        }
    }
    if(!checknotexist($conn, $id)){    //false utente esiste allora puó inserire l'elemento
        $conn->begin_transaction();
        try{
            $stmt = $conn->prepare('UPDATE element SET ceu=? WHERE id=? AND user_id=?');
            $stmt->bind_param('sii', $ceu, $id_el, $id); 
            $stmt->execute();
            $result = $stmt->get_result();
            $conn->commit();
            response("Element updated", true, "");
        }catch(mysqli_sql_exception $exception){
            $mysqli->rollback();
            throw $exception;
        }
    }else{  //false allora l'utente giá esiste
        response("Error in element update", false, "");
    }
        
}elseif($_SERVER["REQUEST_METHOD"] == "GET"){  //Get info about element
    header("Access-Control-Allow-Methods: GET");
    require_once "../Config/DB.php";
    $request = $_SERVER['REQUEST_URI'];
    $ex = explode("/",explode("element.php", $request)[1]);
    error_reporting(E_ERROR | E_PARSE);
    if(strpos($ex[1], '?') !=false ){   //Se la richiesta é una get prendo il la stringa prima della get
        $ex = explode("?", $ex[1]);
        $ex[1] = $ex[0];
    }
    switch (strtolower($ex[1])) {
        case '/':
        case '':{   
            response("Invalid request", false, "");
            break;
        }
        case 'all':{
            req_all($conn, $secret_key);
            break;
        }
        case 'team_id':{
            req_teamid($conn);
            break;
        }
        case 'user_id':{
            req_userid($conn);
            break;
        }
        case 'privilege':{
            req_privilege($conn);
            break;
        }
        case 'google':{
            req_google($conn);
            break;
        }
        case '2fa':{
            req_2fa($conn);
            break;
        }
    }
}elseif($_SERVER["REQUEST_METHOD"] == "PUT"){  //Insert
    header("Access-Control-Allow-Methods: PUT");
    require_once "../Config/DB.php";
    include("crypto/jwt.php");
    $jwt = getBearerToken();
    $id = verify($jwt, $secret_key);
    $data = json_decode(file_get_contents("php://input"), true);
    if($data!=null){
        $ceu = $data["ceu"];    //Elemento
    }
    if(empty($ceu)){
        $ceu = $_PUT["ceu"];
        if(empty($ceu) || empty($id)){
            response("Error sending data", false, "");
        }
    }
    if(!checknotexist($conn, $id)){    //false utente esiste allora puó inserire l'elemento
        $stmt = $conn->prepare('INSERT INTO element(user_id, ceu) VALUES(?,?)');
        $stmt->bind_param('ss', $id, $ceu); 
        $stmt->execute();
        $result = $stmt->get_result();
        response("Element added", true, "");
    }else{  //false allora l'utente giá esiste
        response("Error element", false, "");
    }
}elseif($_SERVER["REQUEST_METHOD"] == "DELETE"){  //Delete
    header("Access-Control-Allow-Methods: DELETE");
    response("No user can be deleted", false, "");
}
function checknotexist($conn, $id){
    $stmt = $conn->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->bind_param('s', $id); 
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0){
        return true;
    }else{
        return false;
    }
}
function checkexist($conn, $id){
    $stmt = $conn->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->bind_param('s', $id); 
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0){
        return false;
    }else{
        return true;
    }
}
function takeid($conn, $email){
    $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->bind_param('s', $email); 
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0){
        return false;
    }else{
        while ($row = $result->fetch_assoc()) {
            return $row["id"];
        }
    }
}
function req_all($conn, $secret_key){
    include("crypto/jwt.php");
    $jwt = getBearerToken();
    $id = verify($jwt, $secret_key);
    if($id != false){
        $stmt = $conn->prepare('SELECT * FROM element WHERE user_id = ?');
        $stmt->bind_param('i', $id); 
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows === 0){
            response("No users found",false, "");
        }else{
            $t = "";
            while ($row = $result->fetch_assoc()) {
                $t.= ($row["id"] . "?" . $row["ceu"]) . ":"; 
            }
            response("User found", true, $t); 
        }
        $stmt->close();
        $result->close();
        $conn->next_result();
    }else{
        header('HTTP/1.1 401 Unauthorized');
        exit;
    }
}
function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    }
    else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        //print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}
function getBearerToken() {
    $headers = getAuthorizationHeader();
    // HEADER: Get the access token from the header
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
    }
    return null;
}
function req_teamid($conn){
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data["user_id"];
    if(empty($id)){
        $id = $_GET["user_id"];
        if(empty($id)){
            response("Error sending data", false, "");
        }
    }
    $stmt = $conn->prepare('SELECT * FROM detusers WHERE user_id = ?');
    $stmt->bind_param('s', $id); 
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0){
        response("No users find", false, "");
    }else{
        $res = "";
        while ($row = $result->fetch_assoc()) {
            if($res != ""){
                $res .=",";
            }
            $res .= $row["team_id"];
        }
        response("User finded", true, $res);
    }
}
function req_userid($conn){
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data["team_id"];
    if(empty($id)){
        $id = $_GET["team_id"];
        if(empty($id)){
            response("Error sending data", false, "");
        }
    }
    $stmt = $conn->prepare('SELECT * FROM detusers WHERE team_id = ?');
    $stmt->bind_param('s', $id); 
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0){
        response("No users find", false, "");
    }else{
        $res = "";
        while ($row = $result->fetch_assoc()) {
            if($res != ""){
                $res .=",";
            }
            $res .= $row["user_id"];
        }
        response("User finded", true, $res);
    }
}
function req_privilege($conn){
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data["user_id"];
    if(empty($id)){
        $id = $_GET["user_id"];
        if(empty($id)){
            response("Error sending data", false, "");
        }
    }
    $stmt = $conn->prepare('SELECT * FROM detusers WHERE user_id = ?');
    $stmt->bind_param('s', $id); 
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0){
        response("No users find", false, "");
    }else{
        $res = "";
        while ($row = $result->fetch_assoc()) {
            if($res != ""){
                $res .=",";
            }
            if($row["privilege"] == ""){
                $res .= "User has no privileges";
            }else{
                $res .= $row["privilege"];
            }
        }
        response("User finded", true, $res);
    }
}
function req_google($conn){
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data["user_id"];
    if(empty($id)){
        $id = $_GET["user_id"];
        if(empty($id)){
            response("Error sending data", false, "");
        }
    }
    $stmt = $conn->prepare('SELECT * FROM detusers WHERE user_id = ?');
    $stmt->bind_param('s', $id); 
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0){
        response("No users find", false, "");
    }else{
        $res = "";
        while ($row = $result->fetch_assoc()) {
            if($res != ""){
                $res .=",";
            }
            $res .= $row["googlesign"];
        }
        response("User finded", true, $res);
    }
}
function req_2fa($conn){
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data["user_id"];
    if(empty($id)){
        $id = $_GET["user_id"];
        if(empty($id)){
            response("Error sending data", false, "");
        }
    }
    $stmt = $conn->prepare('SELECT * FROM detusers WHERE user_id = ?');
    $stmt->bind_param('s', $id); 
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0){
        response("No users find", false, "");
    }else{
        $res = "";
        while ($row = $result->fetch_assoc()) {
            if($res != ""){
                $res .=",";
            }
            $res .= $row["fa"];
        }
        response("User finded", true, $res);
    }
}
function response($message, $status, $response){
	if($status==true){
        http_response_code(200);
    }else{
        http_response_code(400);
    }
    echo json_encode(array("message" => $message, "status" => $status, "response" => $response));
    exit();
}
?>