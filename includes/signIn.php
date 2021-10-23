<?php

session_start();

use aktivgo\chat\database\Database;
use aktivgo\chat\app\HttpResponse;
use aktivgo\chat\app\UserController;
use Firebase\JWT\JWT;

$login = $_POST['login'];
$password = $_POST['password'];

$errorFields = [];
if ($login === '') {
    $errorFields[] = 'login';
}
if ($password === '') {
    $errorFields[] = 'password';
}

if (!empty($errorFields)) {
    echo json_encode([
        'status' => false,
        'message' => "Проверьте правильность полей",
        'fields' => $errorFields
    ]);
    die();
}

$db = Database::getConnection();

$user = UserController::getUserByLoginAndPassword($db, $login, $password);

$_SESSION['user'] = [
    'id' => $user['id'],
    'fullName' => $user['fullName'],
    'email' => $user['email']
];

HttpResponse::toSendResponse([
    'status' => true,
    'token' => JWT::encode($user['id'], $_ENV['KEY'], 'HS256'),
    'message' => 'Вход выполнен успешно'
    ], 200);