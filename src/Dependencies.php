<?php

declare(strict_types=1);

use Auryn\Injector;
use Doctrine\DBAL\Connection;
use trainingAPI\Framework\Configuration;
use trainingAPI\Framework\Dbal\ConnectionFactory;
use trainingAPI\Jwt\DbalTokenRepository;
use trainingAPI\Jwt\JwtAuthenticator;
use trainingAPI\Jwt\TokenRepository;
use trainingAPI\Login\Authenticator;
use trainingAPI\Login\DbalEmailExistsQuery;
use trainingAPI\Login\DbalUserRepository;
use trainingAPI\Login\EmailExistsQuery;
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
$injector->alias(Authenticator::class, JwtAuthenticator::class);
$injector->alias(UserRepository::class, DbalUserRepository::class);
$injector->alias(SessionRepository::class, DbalSessionRepository::class);
$injector->alias(SessionIdExists::class, DbalSessionIdExists::class);
$injector->alias(EmailExistsQuery::class, DbalEmailExistsQuery::class);
$injector->alias(TokenRepository::class, DbalTokenRepository::class);

return $injector;
