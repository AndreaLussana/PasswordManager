<?php
    //Campi della tabella utente sono: email, pwd, salt, key
    //pwd é password hashata con email
    $salt = array();
    // 1. PBKDF2 100000 salt email payload hash della master password
    $master_key = hash_pbkdf2("sha256", $pwd, $email, 100000, 50);
    // 2. PBKDF2 1 salt password payload master key
    $master_password_hash = hash_pbkdf2("sha256", $master_key, $pwd, 1, 50);
    // 3. PBKDF2 100000 salt random payload master password hash
    $stored_salt_pwd = random_bytes(32);
    $stored_salt_pwd = utf8_decode($stored_salt_pwd);
    $stored_pwd = hash_pbkdf2("sha256", $master_password_hash, $stored_salt_pwd, 100000, 50);   //Salvare nel db
    $salt["salt-pwd"] = $stored_salt_pwd;
    ///////////////////////////////////////////////////////////////////////////////////////////
    $stretched_master_key = hash_hkdf('sha256', $master_key, 32, 'aes-256-encryption', '');
    /*echo "|stretched: " . $stretched_master_key;
    $symmetric_key = openssl_random_pseudo_bytes(32);
    echo "|symmetric: "  .$symmetric_key;

    $method = "aes-256-cbc";   
    $iv_length = openssl_cipher_iv_length($method);
    $iv = openssl_random_pseudo_bytes($iv_length);
    echo "|iv: " . $iv;
    $salt["salt-iv"] = utf8_encode($iv);
    $stored_key = openssl_encrypt($symmetric_key,$method,$stretched_master_key, OPENSSL_RAW_DATA ,utf8_encode($iv));     //Salvare nel db
    $stored_salt = serialize($salt);    //Salvare nel db
    echo "Iv normale: " . strlen($iv) . "mentre encode vale: " . strlen(utf8_encode($iv));
    //$data = openssl_decrypt($stored_key,$method,$stretched_master_key,OPENSSL_RAW_DATA,$iv);*/
    $stored_salt = serialize($salt);
    include("abcrypt.php");
    $symmetric_key = openssl_random_pseudo_bytes(32);
    $abCrypt = new abCrypt(bin2hex($stretched_master_key));
    $stored_key = $abCrypt->encrypt($symmetric_key);
    
?>