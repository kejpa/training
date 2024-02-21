<?php

declare(strict_types=1);

return [
// Sessions    
    [
        'GET',
        '/sessions[/]',
        'trainingAPI\Session\SessionController#getAllSessions'
    ],
    [
        'GET',
        '/sessions/{id:\d+}',
        'trainingAPI\Session\SessionController#getSession'
    ],
    [
        'POST',
        '/sessions',
        'trainingAPI\Session\SessionController#addSession'
    ],
    [
        'PUT',
        '/sessions/{id:\d+}',
        'trainingAPI\Session\SessionController#updateSession'
    ],
    [
        'DELETE',
        '/sessions/{id:\d+}',
        'trainingAPI\Session\SessionController#deleteSession'
    ],
    // Login
    [
        'POST',
        '/login',
        'trainingAPI\Login\LoginController#logIn'
    ],
    [
        'GET',
        '/check[/]',
        'trainingAPI\Login\LoginController#check'
    ],
    [
        'POST',
        '/register/',
        'trainingAPI\Login\LoginController#register'
    ],
    [
        'POST',
        '/changePassword/{user}',
        'trainingAPI\Login\LoginController#updatePassword'
    ],
    [
        'GET',
        '/resetPassword/{user}',
        'trainingAPI\Login\LoginController#resetPassword'
    ],
    [
        'POST',
        '/resetPassword/{user}',
        'trainingAPI\Login\LoginController#changePassword'
    ],
//    [
//        'OPTIONS',
//        '/checkToken/',
//        'trainingAPI\Preflight\PreflightController#preFlight'
//    ],
//    [
//        'OPTIONS',
//        '/login[/]',
//        'trainingAPI\Preflight\PreflightController#preFlight'
//    ],
//    [
//        'OPTIONS',
//        '/sessions[/{id:\d+}]',
//        'trainingAPI\Preflight\PreflightController#preFlight'
//    ],
];
