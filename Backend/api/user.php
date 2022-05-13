<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization");
if($_SERVER["REQUEST_METHOD"] == "POST"){   //Update only detusers table
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
        require_once "../Config/DB.php";
        if(!checknotexist($conn, $email)){    //false allora utente esiste e devo controllare le credenziali
            include("crypto/user_login.php");
            $stmt = $conn->prepare('SELECT salt FROM users WHERE email=?'); //Prendo il salt per generare la password e confrontarla
            $stmt->bind_param('s', $email); 
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows === 0){
                response("No users found", false, "");
            }else{
                while ($row = $result->fetch_assoc()) {
                    $salt=$row["salt"];
                }
                $salt = unserialize($salt); //Salt utilizzato per la password dell'utente
                $pass = loginPwd($email, $pwd, $salt);
                $stmt = $conn->prepare('SELECT pwd FROM users WHERE email=?'); //Prendo il salt per generare la password e confrontarla
                $stmt->bind_param('s', $email); 
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $stored_pass=$row["pwd"];
                }
                //echo "stored vale: " . $stored_pass . " mentre pass vale: " . $pass;
                if($stored_pass == $pass){
                    $stmt = $conn->prepare('SELECT skey FROM users WHERE email=?'); //Prendo il salt per generare la password e confrontarla
                    $stmt->bind_param('s', $email); 
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $stored_key=$row["skey"];
                    }
                    $k = loginKey($email, $pwd, $stored_key, $salt);
                    include("crypto/jwt.php");
                    $c_jwt = create(takeid($conn, $email), $secret_key);
                    response("User logged in", true, utf8_encode($k) . ":" . $c_jwt);
                }else{
                    response("Login error", false, "");
                }
            }
        }else{  //false allora l'utente giá esiste
            response("User already exist", false, "");
        }
    }else{
        if($data!=null){
            $id = $data["id"];
            $change = $data["change"];
            $value = $data["value"];
        }
        if(empty($id) || empty($change) || empty($value)){
            $id = $_POST["id"];
            $change = $_POST["change"];
            $value = $_POST["value"];
            if(empty($id) || empty($change) || empty($value)){
                response("Error sending data", false, "");
            }
        }
        require_once "../Config/DB.php";
        if(checkexist($conn, $id)){   
            switch($change){
                case 'team_id':{
                    $stmt = $conn->prepare('UPDATE detusers SET team_id=? WHERE user_id=?');
                    $stmt->bind_param('ii', $value, $id); 
                    $stmt->execute();
                    $result = $stmt->get_result();
                    response("Data updated", true, "");
                    break;
                }
                case 'privilege':{
                    $stmt = $conn->prepare('UPDATE detusers SET privilege=? WHERE user_id=?');
                    $stmt->bind_param('ii', $value, $id); 
                    $stmt->execute();
                    $result = $stmt->get_result();
                    response("Data updated", true, "");
                    break;
                }
                case 'googlesign':{
                    $stmt = $conn->prepare('UPDATE detusers SET googlesign=? WHERE user_id=?');
                    $stmt->bind_param('ii', $value, $id); 
                    $stmt->execute();
                    $result = $stmt->get_result();
                    response("Data updated", true, "");
                    break;
                }
                case 'fa':{
                    $stmt = $conn->prepare('UPDATE detusers SET fa=? WHERE user_id=?');
                    $stmt->bind_param('ii', $value, $id); 
                    $stmt->execute();
                    $result = $stmt->get_result();
                    response("Data updated", true, "");
                    break;
                }
                default:{
                    response("Error change value", false, "");
                }
            }
        }else{
            response("No user found", false, "");
        }
    }
}elseif($_SERVER["REQUEST_METHOD"] == "GET"){  //read id or details of user
    header("Access-Control-Allow-Methods: GET");
    require_once "../Config/DB.php";
    $request = $_SERVER['REQUEST_URI'];
    $ex = explode("/",explode("user.php", $request)[1]);
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
        case 'id':{
            req_id($conn);
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
        $email = $data["email"];
        $pwd = $data["pwd"];
    }
    if(empty($email) || empty($pwd)){
        $email = $_PUT["email"];
        $pwd = $_PUT["pwd"];
        if(empty($email) || empty($pwd)){
            response("Error sending data", false, "");
        }
    }
    require_once "../Config/DB.php";
    if(checknotexist($conn, $email)){    //true allora l'utente non esiste allora puó registrarsi
        include("crypto/user_store.php");
        $stmt = $conn->prepare('INSERT INTO users(email, pwd, skey, salt) VALUES(?,?, ?, ?)');
        $stmt->bind_param('ssss', $email, $stored_pwd, $stored_key, $stored_salt); 
        $stmt->execute();
        $result = $stmt->get_result();
        $id = takeid($conn, $email);
        response("User created", true, "Success");
    }else{  //false allora l'utente giá esiste
        response("User already exist", false, "");
    }
}elseif($_SERVER["REQUEST_METHOD"] == "DELETE"){  //Delete
    header("Access-Control-Allow-Methods: DELETE");
    response("No user can be deleted", false, "");
}
function checknotexist($conn, $email){
    $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->bind_param('s', $email); 
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
function req_id($conn){
    $data = json_decode(file_get_contents("php://input"), true);
    if($data!=null){
        $pwd = $data["pwd"];
    }
    if(empty($pwd)){
        $pwd = $_GET["pwd"];
        if(empty($pwd)){
            response("Error sending data", false, "");
        }
    }
    $stmt = $conn->prepare('SELECT * FROM users WHERE pwd = ?');
    $stmt->bind_param('s', $pwd); 
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0){
        response("No users found",false, "");
    }else{
        while ($row = $result->fetch_assoc()) {
            response("User found", true, $row["id"]); 
        }
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