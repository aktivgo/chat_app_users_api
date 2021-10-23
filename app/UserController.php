<?php

namespace aktivgo\chat\app;

use PDO;

header('Content-type: json/application');

class UserController
{
    // Получает всех пользователей
    public static function getUsers(PDO $db, array $get)
    {
        $page = $get['page'] ?? 1;
        $limit = 3;
        $offset = ($page - 1) * $limit;

        $userList = [];
        $sth = $db->prepare("select * from users where id > 0 limit :offset, :limit");
        $sth->execute(['offset' => $offset, 'limit' => $limit]);

        while ($res = $sth->fetch(PDO::FETCH_ASSOC)) {
            $userList[] = $res;
        }

        HttpResponse::toSendResponse($userList, 200);
    }

    // Получает пользователя по id
    public static function getUserById(PDO $db, string $id)
    {
        $sth = $db->prepare("select * from users where id = :id");
        $sth->execute(['id' => $id]);
        $res = $sth->fetch(PDO::FETCH_ASSOC);

        if (!$res) {
            HttpResponse::toSendResponse(['User not found'], 404);
            die();
        }

        HttpResponse::toSendResponse($res, 200);
    }

    // Получает пользователя по логину и паролю
    public static function getUserByLoginAndPassword(PDO $db, string $login, string $password)
    {
        $sth = $db->prepare("select * from users where login = :login and password = :password");
        $sth->execute(['login' => $login, 'password' => md5($password)]);
        $res = $sth->fetch(PDO::FETCH_ASSOC);

        if (!$res) {
            echo json_encode([
                'status' => false,
                'message' => "Неверный логин или пароль",
                'fields' => ['login', 'password']
            ]);
            die();
        }
        return $res;
    }

    // Добавляет пользователя в БД
    public static function addUser(PDO $db, ?array $data)
    {
        $sth = $db->prepare("insert into users values (null,:fullName, :avatar, :login, :password)");
        $sth->execute($data);

        HttpResponse::toSendResponse([
            'status' => true,
            'id' => $db->lastInsertId(),
            'message' => 'Регистрация прошла успешно'
        ], 201);

    }

    // Проверяет наличие пользователя с данным логином в БД
    public static function isLoginExist(PDO $db, string $login): bool
    {
        $st = $db->prepare("select * from users where login = :login");
        $st->execute(['login' => $login]);
        if ($st->fetch(PDO::FETCH_ASSOC)) {
            return true;
        }
        return false;
    }
}