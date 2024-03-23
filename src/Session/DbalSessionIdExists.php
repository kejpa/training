<?php

declare (strict_types=1);

namespace trainingAPI\Session;

use Doctrine\DBAL\Connection;

/**
 * Description of DbalSessionIdExists
 *
 * @author kjell
 */
final class DbalSessionIdExists implements SessionIdExists {

    private $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function execute(int $id, int $userId): bool {

        $qb = $this->connection->createQueryBuilder();
        $qb->select('*');
        $qb->from('sessions');
        $qb->where("id = {$qb->createNamedParameter($id)}")
                ->andWhere("userid = {$qb->createNamedParameter($userId)}");

        $antal = $qb->executeStatement();

        return (bool) $antal;
    }
}
