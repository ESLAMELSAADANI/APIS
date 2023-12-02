<?php
require 'vendor/autoload.php';  

use Firebase\JWT\JWT;

// Define your secret key and other data
$secretKey = 'your_secret_key'; // Replace with your actual secret key
$issuer = 'your_api';
$audience = 'your_client';
$issuedAt = time();
$expirationTime = $issuedAt + 3600; // Token expires in 1 hour

// Create a data array to be encoded in the token
$data = [
    'iss' => $issuer,
    'aud' => $audience,
    'iat' => $issuedAt,
    'exp' => $expirationTime,
];

// Generate the token
$token = JWT::encode($data, $secretKey, 'HS256');

// Output the token
// echo $token;
?>
