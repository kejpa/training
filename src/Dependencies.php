<?php

declare(strict_types=1);

use kejpa\training\Framework\Configuration;
use kejpa\training\Framework\Dbal\ConnectionFactory;
use Auryn\Injector;
use Doctrine\DBAL\Connection;

$injector = new Injector();
$injector->define(Configuration::class, [":file" => ROOT_DIR . '/src/settings.json']);

$injector->delegate(Connection::class, function () use ($injector): Connection {
    $factory = $injector->make(ConnectionFactory::class);
return $factory->create();
});
$injector->share(Connection::class);
