<?php

declare (strict_types=1);

namespace trainingAPI\Framework\Dbal;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use kejpa\training\Framework\Configuration as Conf;

class ConnectionFactory {

    private $config;

    public function __construct(Conf $configuration) {
        $this->config = $configuration;
    }

    public function create(): Connection {
        return DriverManager::getConnection(
                        ['dbname' => $this->config->get("Database.db"),
                            'user' => $this->config->get("Database.user"),
                            'password' => $this->config->get("Database.password"),
                            'host' => $this->config->get("Database.host"),
                            'driver' => 'pdo_mysql'],
                        new Configuration()
        );
    }

}
