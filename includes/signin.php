<?php

session_start();

use aktivgo\chat\database\Database;
use aktivgo\chat\app\HttpResponse;
use aktivgo\chat\app\UserController;

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
    $response = [
        'message' => "Проверьте правильность полей",
        'fields' => $errorFields
    ];
    HttpResponse::toSendResponse($response, 400);
    die();
}

$db = Database::getConnection();

$user = UserController::getUserByLoginAndPassword($db, $login, $password);

$_SESSION['user'] = [
    'id' => $user['id'],
    'fullName' => $user['fullName'],
    'email' => $user['email']
];

HttpResponse::toSendResponse([], 200);