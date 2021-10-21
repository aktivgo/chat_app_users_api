<?php

session_start();

use aktivgo\chat\app\HttpResponse;
use aktivgo\chat\app\UserController;
use aktivgo\chat\database\Database;

$fullName = $_POST['fullName'];
$avatar = null;
$login = $_POST['login'];
$password = $_POST['password'];
$passwordConfirm = $_POST['passwordConfirm'];

$db = Database::getConnection();

if (UserController::isLoginExist($db, $login)) {
    HttpResponse::toSendResponse([
        'status' => false,
        'message' => 'Такой логин уже существует',
        'fields' => ['login']
    ], 400);
    die();
}

$errorFields = [];
if ($fullName === '') {
    $errorFields[] = 'fullName';
}
if ($login === '') {
    $errorFields[] = 'login';
}
if ($password === '') {
    $errorFields[] = 'password';
}
if ($passwordConfirm === '') {
    $errorFields[] = 'passwordConfirm';
}

if (!empty($errorFields)) {
    HttpResponse::toSendResponse([
        'status' => false,
        'message' => 'Проверьте правильность полей',
        'fields' => $errorFields
    ], 400);
    die();
}

if ($password != $passwordConfirm) {
    HttpResponse::toSendResponse([
        'status' => false,
        'message' => 'Пароли не совпадают'
    ], 400);
    die();
}

$password = md5($password);

UserController::addUser($db, [
    'fullName' => $fullName,
    'avatar' => $avatar,
    'login' => $login,
    'password' => $password
]);


HttpResponse::toSendResponse([
    'status' => true,
    'message' => 'Регистрация прошла успешно'
    ], 200);