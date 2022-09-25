<?php

declare(strict_types=1);

use Auryn\Injector;
use Doctrine\DBAL\Connection;
use trainingAPI\Framework\Configuration;
use trainingAPI\Framework\Dbal\ConnectionFactory;
use trainingAPI\Login\DbalUserRepository;
use trainingAPI\Login\UserRepository;
use trainingAPI\Session\DbalSessionIdExists;
use trainingAPI\Session\DbalSessionRepository;
use trainingAPI\Session\SessionIdExists;
use trainingAPI\Session\SessionRepository;

$injector = new Injector();
$injector->define(Configuration::class, [":file" => ROOT_DIR . '/src/settings.json']);

$injector->delegate(Connection::class, function () use ($injector): Connection {
    $factory = $injector->make(ConnectionFactory::class);
    return $factory->create();
});
$injector->share(Connection::class);
$injector->alias(UserRepository::class, DbalUserRepository::class);
$injector->alias(SessionRepository::class, DbalSessionRepository::class);
$injector->alias(SessionIdExists::class, DbalSessionIdExists::class);

return $injector;
