<?php

declare(strict_types=1);

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tracy\Debugger;
use function FastRoute\simpleDispatcher;

// Definiera ROOT_DIR globalt som aktuell mapp
define('ROOT_DIR', dirname(__DIR__));

// Autoladda alla composer-paket
require ROOT_DIR . '/vendor/autoload.php';

// Sätt igång debugging
Debugger::enable();
//Debugger::enable(Debugger::DEVELOPMENT);
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

// Hantera ruttinfo
switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
    case Dispatcher::METHOD_NOT_ALLOWED:
        $content = file_get_contents(ROOT_DIR . "/src/info/info.html");
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
    throw new Exception('Controller methods must return a Response object');
}

// Förbered utdata
$response->prepare($request);
// Skicka utdata
$response->send();
