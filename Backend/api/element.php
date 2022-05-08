<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization");
if($_SERVER["REQUEST_METHOD"] == "POST"){   //Update info about element
    header("Access-Control-Allow-Methods: POST");
    $data = json_decode(file_get_contents("php://input"), true);
    if(empty($data["id"]) && empty($_POST["id"])){
        if($data!=null){
            $email = $data["email"];
            $pwd = $data["pwd"];
        }
        if(empty($email) || empty($pwd)){
            $email = $_POST["email"];
            $pwd = $_POST["pwd"];
            if(empty($email) || empty($pwd)){
                response("Error sending data", false, "");
            }
        }
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
            req_all($conn);
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
    $data = json_decode(file_get_contents("php://input"), true);
    if($data!=null){
        $id = $data["id"];  //Da prendere poi tramite il JWT
        $ceu = $data["ceu"];    //Elemento
    }
    if(empty($id) || empty($ceu)){
        $id = $_PUT["id"];
        $ceu = $_PUT["ceu"];
        if(empty($id) || empty($ceu)){
            response("Error sending data", false, "");
        }
    }
    require_once "../Config/DB.php";
    if(!checknotexist($conn, $id)){    //false utente esiste allora puó inserire l'elemento
        $stmt = $conn->prepare('INSERT INTO element(user_id, ceu) VALUES(?,?)');
        $stmt->bind_param('ss', $id, $ceu); 
        $stmt->execute();
        $result = $stmt->get_result();
        response("Element added", true, "");
    }else{  //false allora l'utente giá esiste
        response("User not exist", false, "");
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
function req_all($conn){
    $data = json_decode(file_get_contents("php://input"), true);
    if($data!=null){
        $id = $data["id"];  //Da cambiare poi con il jwt
    }
    if(empty($id)){
        $id = $_GET["id"];
        if(empty($id)){
            response("Error sending data", false, "");
        }
    }
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
    echo json_encode(array("message" => $message, "status" => $status, "response" => $response));
    exit();
}
?>