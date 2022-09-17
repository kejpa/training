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
        'sessions/{id:\d+}',
        'trainingAPI\Session\SessionController#deleteSession'
    ],
    // Login
    [
        'POST',
        'login/',
        'Kejpa\Trainingapi\Login\LoginController#logIn'
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
        'POST',
        'resetPassword/{user}',
        'Kejpa\Trainingapi\Login\LoginController#resetPassword'
    ],
    [
        'POST',
        'checkToken/',
        'Kejpa\Trainingapi\Login\LoginController#checkToken'
    ],
];
