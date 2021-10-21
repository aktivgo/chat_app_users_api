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
    echo json_encode([
        'status' => false,
        'message' => 'Такой логин уже существует',
        'fields' => ['login']
    ]);
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
    echo json_encode([
        'status' => false,
        'message' => 'Проверьте правильность полей',
        'fields' => $errorFields
    ]);
    die();
}

if ($password != $passwordConfirm) {
    echo json_encode([
        'status' => false,
        'message' => 'Пароли не совпадают'
    ]);
    die();
}

$password = md5($password);

UserController::addUser($db, [
    'fullName' => $fullName,
    'avatar' => $avatar,
    'login' => $login,
    'password' => $password
]);