<?php

declare (strict_types=1);

namespace trainingAPI\Login;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use trainingAPI\Login\EmailExistsQuery;

/**
 * Description of DbalEmailExistsQuery
 *
 * @author kjell
 */
final class DbalEmailExistsQuery implements EmailExistsQuery {

    private $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function execute(string $email): bool {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('email');
        $qb->from('users');
        $qb->where('email=' . $qb->createNamedParameter($email));

        $stmt = $qb->executeQuery();

        return boolval($stmt->rowCount());
    }
}
