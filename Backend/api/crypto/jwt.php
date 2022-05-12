<?php
require "../../vendor/autoload.php";
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
function verify($jwt, $secret_key){
    $token = JWT::decode($jwt, new Key($secret_key, 'HS512'));
    $now = new DateTimeImmutable();
    $serverName = "lussana.altervista.org";
    if ($token->iss !== $serverName || $token->nbf > $now->getTimestamp() || $token->exp < $now->getTimestamp())
    {
        return false;
    }else{
        return $token->id;
    }
}
function create($id, $secret_key){
    $date   = new DateTimeImmutable();
    $expire_at     = $date->modify('+1 hour')->getTimestamp();   
    $domainName = "lussana.altervista.org";
    $request_data = [
        'iat'  => $date->getTimestamp(),         // Issued at: time when the token was generated
        'iss'  => $domainName,                       // Issuer
        'nbf'  => $date->getTimestamp(),         // Not before
        'exp'  => $expire_at,                           // Expire
        'id' => $id,                     // id user
    ];
    return JWT::encode(
        $request_data,
        $secret_key,
        'HS512'
    );
}


?>