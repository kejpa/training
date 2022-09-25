<?php

declare(strict_types=1);

return [
// Sessions    
    [
        'GET',
        '/sessions/',
        'trainingAPI\Session\SessionController#getAllSessions'
    ],
    [
        'GET',
        '/sessions/{id:\d+}',
        'trainingAPI\Session\SessionController#getSession'
    ],
    [
        'POST',
        '/sessions/',
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
        '/login/',
        'trainingAPI\Login\LoginController#logIn'
    ],
    [
        'POST',
        'register/',
        'Kejpa\Trainingapi\Login\LoginController#register'
    ],
    [
        'POST',
        'register/{user}',
        'Kejpa\Trainingapi\Login\LoginController#updatePassword'
    ],
    [
        'GET',
        '/resetPassword/{user}',
        'trainingAPI\Login\LoginController#resetPassword'
    ],
    [
        'POST',
        'checkToken/',
        'Kejpa\Trainingapi\Login\LoginController#checkToken'
    ],
];
