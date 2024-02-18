<?php

declare(strict_types=1);

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use trainingAPI\Exceptions\AuthenticationException;
use trainingAPI\Exceptions\AuthorizationException;
use trainingAPI\Exceptions\ValidationException;
use function FastRoute\simpleDispatcher;

// Definiera ROOT_DIR globalt som aktuell mapp
define('ROOT_DIR', dirname(__DIR__));

// Autoladda alla composer-paket
require ROOT_DIR . '/vendor/autoload.php';

// Skapa ett request-objekt med alla inskickade parametrar
$request = Request::createFromGlobals();

// Skapa ett dispatcher-objekt som sköter routingen
$dispatcher = simpleDispatcher(
        function (RouteCollector $r) {
            // Alla rutter definieras i Routes-filen
            $routes = include(ROOT_DIR . '/src/Routes.php');
            foreach ($routes as $route) {
                $r->addRoute(...$route);
            }
        }
);

// Hämta ruttinfo från dispatchern
$routeInfo = $dispatcher->dispatch(
        $request->getMethod(),
        $request->getPathInfo()
);
try {
// Hantera ruttinfo
    switch ($routeInfo[0]) {
        case Dispatcher::NOT_FOUND:
        case Dispatcher::METHOD_NOT_ALLOWED:
            $content = file_get_contents(ROOT_DIR . "/src/Info/info.html");
            $response = new Response($content);
            break;
        case Dispatcher::FOUND:
            [$controllerName, $method] = explode('#', $routeInfo[1]);
            $vars = $routeInfo[2];
            $injector = include(ROOT_DIR . '/src/Dependencies.php');
            $controller = $injector->make($controllerName);
            $response = $controller->$method($request, $vars);
            break;
    }
// Vi fick fel objekttyp i retur! Något gick fel!
    if (!$response instanceof Response) {
        $response = new JsonResponse('Oväntat fel inträffade', 500);
    }
} catch (AuthenticationException $ex) {
    $error = new stdClass();
    $error->message = [$ex->getMessage()];
    $response = new JsonResponse($error, 401);
} catch (AuthorizationException $ex) {
    $error = new stdClass();
    $error->message = [$ex->getMessage()];
    $response = new JsonResponse($error, 401);
} catch (ValidationException $ex) {
    $error = new stdClass();
    $error->message = $ex->getAllMessages();
    $response = new JsonResponse($error, 400);
} catch (Exception $ex) {
    var_dump($ex);exit;
    $error = new stdClass();
    $error->message = [$ex->getMessage()];
    $error->trace=$ex->getTrace();
    $response = new JsonResponse($error, 400);
}
$response->headers->add(['Access-Control-Allow-Origin'=>'*']);
// Förbered utdata
$response->prepare($request);
// Skicka utdata
$response->send();
