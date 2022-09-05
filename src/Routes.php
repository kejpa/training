<?php

declare(strict_types=1);

return [
// Sessions    
    [
        'GET',
        'sessions/',
        'Kejpa\Trainingapi\Session\SesssionController#getAllSessions'
    ],
    [
        'GET',
        'sessions/{id:\d+}',
        'Kejpa\Trainingapi\Session\SesssionController#getSession'
    ],
    [
        'POST',
        'sessions/',
        'Kejpa\Trainingapi\Session\SesssionController#addSession'
    ],
    [
        'PUT',
        'sessions/{id:\d+}',
        'Kejpa\Trainingapi\Session\SesssionController#updateSession'
    ],
    [
        'DELETE',
        'sessions/{id:\d+}',
        'Kejpa\Trainingapi\Session\SesssionController#deleteSession'
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
