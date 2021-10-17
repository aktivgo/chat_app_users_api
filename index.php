<?php

session_start();

use aktivgo\chat\app\Activation;
use aktivgo\chat\app\UserController;
use aktivgo\chat\app\HttpResponse;
use aktivgo\chat\database\Database;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . "/composer/vendor/autoload.php";

try {
    // Роут для /users
    $routeUsers = new Route('/users');
    // Роут для /users/id
    $routeUsersId = new Route('/users/{id}', [], ['id' => '\\d+']);
    // Роут для подтверждения почты
    $routeUsersActivation = new Route('/users/activation');
    $routeIndexTemplate = new Route('/');

    $routes = new RouteCollection();
    $routes->add('getUsers', $routeUsers);
    $routes->add('getUser', $routeUsersId);
    $routes->add('userActivation', $routeUsersActivation);
    $routes->add('index', $routeIndexTemplate);

    $context = new RequestContext();
    $context->fromRequest(Request::createFromGlobals());

    $matcher = new UrlMatcher($routes, $context);
    $parameters = $matcher->match($context->getPathInfo());
} catch (Exception $e) {
    HttpResponse::toSendResponse(['The request is incorrect'], 404);
    return;
}

$db = Database::getConnection();

if ($parameters['_route'] === 'userActivation') {
    $token = $_GET['token'];
    if (!$token) {
        HttpResponse::toSendResponse(['The request is incorrect'], 404);
    }
    Activation::confirmEmail($token);
    return;
}

if($parameters['_route'] === 'index') {
    require_once 'templates/index-template.php';
    return;
}

$data = file_get_contents('php://input');
$data = json_decode($data, true);

if (!$parameters['id']) {
    if ($context->getMethod() === 'GET') {
        UserController::getUsers($db, $_GET);
        return;
    }
    if ($context->getMethod() === 'POST') {
        UserController::addUser($db, $data);
        return;
    }

    HttpResponse::toSendResponse(['The request is incorrect'], 404);
    return;
}

if ($context->getMethod() === 'GET') {
    UserController::getUserById($db, $parameters['id']);
    return;
}

if ($context->getMethod() === 'PUT') {
    $data['id'] = $parameters['id'];
    UserController::updateUser($db, $data);
    return;
}

if ($context->getMethod() === 'DELETE') {
    UserController::deleteUser($db, $parameters['id']);
    return;
}