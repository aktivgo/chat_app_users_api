<?php

use aktivgo\chat\app\HttpResponse;
use aktivgo\chat\app\UserController;
use aktivgo\chat\database\Database;
use Firebase\JWT\JWT;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . "/composer/vendor/autoload.php";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');

try {
    $routeSignin = new Route('/signin');
    $routeSignup = new Route('/signup');
    $routeLogout = new Route('/logout');
    $routeAuthorization= new Route('/authorization');

    $routes = new RouteCollection();
    $routes->add('signin', $routeSignin);
    $routes->add('signup', $routeSignup);
    $routes->add('logout', $routeLogout);
    $routes->add('authorization', $routeAuthorization);

    $context = new RequestContext();
    $context->fromRequest(Request::createFromGlobals());

    $matcher = new UrlMatcher($routes, $context);
    $parameters = $matcher->match($context->getPathInfo());
} catch (Exception $e) {
    HttpResponse::toSendResponse([
        'status' => false,
        'message' => 'The request is incorrect'
    ], 404);
    return;
}

$db = Database::getConnection();

if($parameters['_route'] === 'signin') {
    require_once 'includes/signIn.php';
    return;
}

if($parameters['_route'] === 'signup') {
    require_once 'includes/signUp.php';
    return;
}

if($parameters['_route'] === 'authorization') {
    $id = JWT::decode($_POST['token'], $_ENV['KEY'], ['HS256']);
    UserController::getUserById($db, $id);
    return;
}