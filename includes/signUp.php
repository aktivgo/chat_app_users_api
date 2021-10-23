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
        'message' => 'Пароли не совпадают',
        'fields' => ['password', 'passwordConfirm']
    ]);
    die();
}

if (isset($_FILES['avatar'])) {
    /*var_dump($_FILES);
    die();*/
    $path = 'uploads/' . time() . $_FILES['avatar']['name'];
    if (!move_uploaded_file($_FILES['avatar']['tmp_name'], '../' . $path)) {
        echo json_encode([
            'status' => false,
            'message' => 'Ошибка при загрузке изображения',
            'fields' => []
        ]);
        die();
    }
}

$password = md5($password);

UserController::addUser($db, [
    'fullName' => $fullName,
    'avatar' => $avatar,
    'login' => $login,
    'password' => $password
]);