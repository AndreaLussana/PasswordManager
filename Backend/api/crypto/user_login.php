<?php
function loginPwd($email, $pwd, $salt){
    $master_key = hash_pbkdf2("sha256", $pwd, $email, 100000, 50);
    $master_password_hash = hash_pbkdf2("sha256", $master_key, $pwd, 1, 50);
    $pass = hash_pbkdf2("sha256", $master_password_hash, $salt["salt-pwd"], 100000, 50);  //Password da confrontare
    return $pass;
}
function loginKey($email, $pwd, $stored_key, $salt){
    echo "|Stored key: " . $stored_key;
    $master_key = hash_pbkdf2("sha256", $pwd, $email, 100000, 50);
    $stretched_master_key = hash_hkdf('sha256', $master_key, 32, 'aes-256-encryption', '');
    echo "|stretched: " . $stretched_master_key;
    $method = "aes-256-cbc";   
    echo "|iv: " . $salt["salt-iv"];
    $symmetric_key = openssl_decrypt($stored_key,$method,$stretched_master_key,OPENSSL_RAW_DATA,$salt["salt-iv"]);
    echo "|symmetric: " . $symmetric_key;
    echo "|Error: " . openssl_error_string();
    return $symmetric_key;
} 
?>