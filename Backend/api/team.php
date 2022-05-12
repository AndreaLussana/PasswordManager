<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization");
if($_SERVER["REQUEST_METHOD"] == "POST"){   //Update team data
    header("Access-Control-Allow-Methods: POST");
    $data = json_decode(file_get_contents("php://input"), true);
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
    if(checkexisteam($conn, $id)){   
        switch($change){
            case 'name':{
                $stmt = $conn->prepare('UPDATE team SET name=? WHERE id=?');
                $stmt->bind_param('ii', $value, $id); 
                $stmt->execute();
                $result = $stmt->get_result();
                response("Data updated", true, $response);
                break;
            }
            case 'admin_id':{
                $stmt = $conn->prepare('UPDATE team SET admin_id=? WHERE id=?');
                $stmt->bind_param('ii', $value, $id); 
                $stmt->execute();
                $result = $stmt->get_result();
                response("Data updated", true, $response);
                break;
            }
            default:{
                response("Error change value", false, "");
            }
        }
    }else{
        response("No team found", false, "");
    }
}elseif($_SERVER["REQUEST_METHOD"] == "GET"){  //Read data of team
    header("Access-Control-Allow-Methods: GET");
    require_once "../Config/DB.php";
    $request = $_SERVER['REQUEST_URI'];
    $ex = explode("/",explode("team.php", $request)[1]);
    error_reporting(E_ERROR | E_PARSE);
    if(strpos($ex[1], '?') !=false ){   //Se la richiesta é una get prendo il la stringa prima della get
        $ex = explode("?", $ex[1]);
        $ex[1] = $ex[0];
    }
    switch (strtolower($ex[1])) {
        case '/':
        case '':{   
            response("Invalid request", false, "Case / or '' ");
            break;
        }
        case 'id':{
            req_id($conn);
            break;
        }
        case 'name':{
            req_name($conn);
            break;
        }
        case 'admin_id':{
            req_adminid($conn);
            break;
        }
        default:{   
            response("Invalid request", false, "Default");
            break;
        }
    }
}elseif($_SERVER["REQUEST_METHOD"] == "PUT"){  //Insert
    header("Access-Control-Allow-Methods: PUT");
    $data = json_decode(file_get_contents("php://input"), true);
    if($data!=null){
        $name = $data["name"];
        $admin = $data["admin"];
    }
    if(empty($name) || empty($admin)){
        $name = $_PUT["name"];
        $admin = $_PUT["admin"];
        if(empty($name) || empty($admin)){
            response("Error sending data", false, "Request to insert a team failed");
        }
    }
    require_once "../Config/DB.php";
    if(checknotexist($conn, $name)){    //true allora l'utente non esiste allora puó registrarsi
        $stmt = $conn->prepare('INSERT INTO team(name, admin_id) VALUES(?,?)');
        $stmt->bind_param('si', $name, $admin_id); 
        $stmt->execute();
        $result = $stmt->get_result();
        response("Team created", true, "Success");
    }else{  //false allora l'utente giá esiste
        response("User already exist", false, "");
    }
}elseif($_SERVER["REQUEST_METHOD"] == "DELETE"){  //Delete
    header("Access-Control-Allow-Methods: DELETE");
    echo json_encode(array("message" => "No team can be deleted", "status" => false));
}
function checkexisteam($conn, $id){
    $stmt = $conn->prepare('SELECT * FROM team WHERE id = ?');
    $stmt->bind_param('s', $id); 
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0){
        return false;
    }else{
        return true;
    }
}
function checknotexist($conn, $name){
    $stmt = $conn->prepare('SELECT * FROM team WHERE name = ?');
    $stmt->bind_param('s', $name); 
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0){
        return true;
    }else{
        return false;
    }
}
function req_id($conn){ //Prendo id del team dal suo nome 
    $data = json_decode(file_get_contents("php://input"), true);
    if($data!=null){
        $name = $data["name"];
    }
    if(empty($name)){
        $name = $_GET["name"];
        if(empty($name)){
            response("Error sending data", false, "Requested id from team, given name");
        }
    }
    $stmt = $conn->prepare('SELECT * FROM team WHERE name = ?');
    $stmt->bind_param('s', $name); 
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0){
        response("No team found",false, "Requested id from team, given name");
    }else{
        while ($row = $result->fetch_assoc()) {
            response("Team found", true, $row["id"]); 
        }
    }
    $stmt->close();
    $result->close();
    $conn->next_result();
}
function req_name($conn){ 
    $data = json_decode(file_get_contents("php://input"), true);
    if($data!=null){
        $id = $data["id"];
    }
    if(empty($id)){
        $id = $_GET["id"];
        if(empty($id)){
            response("Error sending data", false, "Requested name from team, given id");
        }
    }
    $stmt = $conn->prepare('SELECT * FROM team WHERE id = ?');
    $stmt->bind_param('s', $id); 
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0){
        response("No team found",false, "Requested name from team, given id");
    }else{
        while ($row = $result->fetch_assoc()) {
            response("Team found", true, $row["name"]); 
        }
    }
    $stmt->close();
    $result->close();
    $conn->next_result();
}
function req_adminid($conn){ 
    $data = json_decode(file_get_contents("php://input"), true);
    if($data!=null){
        $id = $data["id"];
    }
    if(empty($id)){
        $id = $_GET["id"];
        if(empty($id)){
            response("Error sending data", false, "Requested admin_id from team, given id");
        }
    }
    $stmt = $conn->prepare('SELECT * FROM team WHERE id = ?');
    $stmt->bind_param('s', $id); 
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 0){
        response("No team found",false, "Requested admin_id from team, given id");
    }else{
        while ($row = $result->fetch_assoc()) {
            response("Team found", true, $row["admin_id"]); 
        }
    }
    $stmt->close();
    $result->close();
    $conn->next_result();
}
function response($message, $status, $response){
    echo json_encode(array("message" => $message, "status" => $status, "response" => $response));
    exit();
}
?>