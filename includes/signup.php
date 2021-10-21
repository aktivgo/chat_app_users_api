<?php

session_start();

use aktivgo\chat\app\HttpResponse;
use aktivgo\chat\app\UserController;
use aktivgo\chat\database\Database;

$fullName = $_POST['fullName'];
$login = $_POST['login'];
$email = $_POST['email'];
$avatar = null;
$password = $_POST['password'];
$passwordConfirm = $_POST['passwordConfirm'];

$db = Database::getConnection();

if (UserController::isLoginExist($db, $login)) {
    $response = [
        'message' => 'Такой логин уже существует',
        'fields' => ['login']
    ];
    HttpResponse::toSendResponse($response, 400);
    die();
}

$errorFields = [];
if ($fullName === '') {
    $errorFields[] = 'fullName';
}
if ($login === '') {
    $errorFields[] = 'login';
}
if ($email != '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errorFields[] = 'email';
}
if ($password === '') {
    $errorFields[] = 'password';
}
if ($passwordConfirm === '') {
    $errorFields[] = 'passwordConfirm';
}

if (!empty($errorFields)) {
    $response = [
        'message' => 'Проверьте правильность полей',
        'fields' => $errorFields
    ];
    HttpResponse::toSendResponse($response, 400);
    die();
}

if ($password != $passwordConfirm) {
    HttpResponse::toSendResponse(['Пароли не совпадают'], 400);
    die();
}

$password = md5($password);

UserController::addUser($db, [
    'fullName' => $fullName,
    'login' => $login,
    'email' => $email,
    'avatar' => $avatar,
    'password' => $password,
]);

HttpResponse::toSendResponse(['Регистрация прошла успешно'], 200);