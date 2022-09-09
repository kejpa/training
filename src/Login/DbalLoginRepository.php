<?php

declare (strict_types=1);

namespace trainingAPI\Login;

use Doctrine\DBAL\Connection;

/**
 * Description of DbalLoginRepository
 *
 * @author kjell
 */
final class DbalLoginRepository implements LoginRepository {

    private $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function getUserByToken(string $token): ?User {
        $qb = $this->connection->createQueryBuilder();
        $qb->addSelect("id")
                ->addSelect("email")
                ->addSelect("firstname")
                ->addSelect("lastname")
                ->addSelect("password")
                ->addSelect("token")
                ->addSelect("tokendate")
                ->addSelect("resettoken")
                ->addSelect("resetdate");
        $qb->from("users");
        $qb->where('token=' . $qb->createNamedParameter($token));

        $stmt = $qb->execute();
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        return User::createFromRow($row);
    }

}
