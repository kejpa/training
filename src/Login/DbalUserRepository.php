<?php

declare (strict_types=1);

namespace trainingAPI\Login;

use Doctrine\DBAL\Connection;

/**
 * Description of DbalLoginRepository
 *
 * @author kjell
 */
final class DbalUserRepository implements UserRepository {

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

    public function getUserByEmail(string $email): ?User {
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
        $qb->where('email=' . $qb->createNamedParameter($email));

        $stmt = $qb->execute();
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        return User::createFromRow($row);
    }

    public function updateUser(User $user): int {
        $qb = $this->connection->createQueryBuilder();
        $qb->update('users');
        $qb->set('email', $qb->createNamedParameter($user->getEmail()))
                ->set('firstname', $qb->createNamedParameter($user->getFirstname()))
                ->set('lastname', $qb->createNamedParameter($user->getLastname()))
                ->set('password', $qb->createNamedParameter($user->getPassword()))
                ->set('token', $qb->createNamedParameter($user->getToken()))
                ->set('tokendate', $qb->createNamedParameter($user->getTokenDate()->format("Y-m-d")))
                ->set('resettoken', $qb->createNamedParameter($user->getResetToken()))
                ->set('resetdate', $qb->createNamedParameter($user->getResetDate()->format("Y-m-d")));
        $qb->where('id=' . $qb->createNamedParameter($user->getId()));
//        var_dump($qb->getSQL(), $qb->getParameters());        exit;
        return $qb->executeStatement();
    }

}
