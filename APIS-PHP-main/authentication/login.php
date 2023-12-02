<?php
// Connect to the database
include "../connectDB.php";


$host = "localhost";
$username = "root";
$password = "";
$database = "space_app";

// Create a new database connection
$connection = new mysqli($host, $username, $password, $database);

require_once '../vendor/autoload.php';

use Firebase\JWT\JWT;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $errors = [];

    if (empty($email)) {
        $errors[] = 'Email is required';
    }
    if (empty($password)) {
        $errors[] = 'Password is required';
    }

    if (!empty($errors)) {
        echo json_encode(['error' => $errors, 'message' => '', 'data' => []]);
    } else {
        $query = $con->prepare("SELECT * FROM `users` WHERE `email` = :email");
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $data = ['id' => $user['id'], 'username' => $user['username'], 'email' => $email, 'password' => $password];
            if ($password === $user['password']) {

                //for token
                $secret_key_r = bin2hex(random_bytes(32));
                $secret_key = $secret_key_r;
                $exp_time = time() + 300;
                $token_data = [
                    "user_id" => $user['id'],
                    "password" => $user['password'],
                    "exp" => $exp_time
                ];
                //create new token expiration after 5 minutes
                $token = 'Bearer ' . $user['id'] . '|' . JWT::encode($token_data, $secret_key, 'HS256');
                $token_to_insert = $connection->real_escape_string($token);

                $user_id = $user['id'];
                $sql = "INSERT INTO token (user_id, token) VALUES ('$user_id', '$token')";

                if ($connection->query($sql) === TRUE) {
                    // echo "Token inserted successfully!";die;
                } else {
                    // echo "Error: " . $sql . "<br>" . $connection->error;
                }
                $connection->close();

                //add token key in array data
                $data['token'] = $token;

                //desplay respons
                echo json_encode(['error' => $errors, 'message' => 'Login successfully', 'data' => $data]);
            } else {
                echo json_encode(['error' => $errors, 'message' => 'incorrect password', 'data' => []]);
            }
        } else {
            echo json_encode(['error' => $errors, 'message' => 'incorrect email or password', 'data' => []]);
        }
    }
} else {
    echo json_encode(['message' => 'plz type valid method']);
}
