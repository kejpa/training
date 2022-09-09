<?php

declare(strict_types=1);

use Auryn\Injector;
use Doctrine\DBAL\Connection;
use trainingAPI\Framework\Configuration;
use trainingAPI\Framework\Dbal\ConnectionFactory;
use trainingAPI\Login\DbalLoginRepository;
use trainingAPI\Login\LoginRepository;
use trainingAPI\Session\DbalSessionRepository;
use trainingAPI\Session\SessionRepository;

$injector = new Injector();
$injector->define(Configuration::class, [":file" => ROOT_DIR . '/src/settings.json']);

$injector->delegate(Connection::class, function () use ($injector): Connection {
    $factory = $injector->make(ConnectionFactory::class);
    return $factory->create();
});
$injector->share(Connection::class);
$injector->alias(LoginRepository::class, DbalLoginRepository::class);
$injector->alias(SessionRepository::class, DbalSessionRepository::class);

return $injector;
