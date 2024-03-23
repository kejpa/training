<?php

declare (strict_types=1);

namespace trainingAPI\Login;

use Doctrine\DBAL\Connection;
use trainingAPI\Jwt\RefreshToken;

/**
 * Description of DbalLoginRepository
 *
 * @author kjell
 */
final class DbalUserRepository implements UserRepository {

    public function __construct(private Connection $connection) {
        
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

        $stmt = $qb->executeQuery();
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
        $qb->where("email= {$qb->createNamedParameter($email)}");

        $stmt = $qb->executeQuery();
        $row = $stmt->fetchAssociative();

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
                ->set('tokendate', $qb->createNamedParameter($user->getTokenDate() === null ? null : $user->getTokenDate()->format("Y-m-d H:i:s")))
                ->set('resettoken', $qb->createNamedParameter($user->getResetToken()))
                ->set('resetdate', $qb->createNamedParameter($user->getResetDate() === null ? null : $user->getResetDate()->format("Y-m-d H:i:s")));
        $qb->where('id=' . $qb->createNamedParameter($user->getId()));

        return $qb->executeStatement();
    }

    public function addUser(User $user): int {
        $qb = $this->connection->createQueryBuilder();
        $qb->insert('users');
        $qb->values(['email' => $qb->createNamedParameter($user->getEmail()),
            'firstname' => $qb->createNamedParameter($user->getFirstname()),
            'lastname' => $qb->createNamedParameter($user->getLastname()),
            'password' => $qb->createNamedParameter($user->getPassword()),
            'token' => $qb->createNamedParameter($user->getToken()),
            'tokendate' => $qb->createNamedParameter($user->getTokenDate()->format("Y-m-d H:i:s"))
        ]);

        $qb->executeStatement();

        return (int) $qb->getConnection()->lastInsertId();
    }

    public function getUserByRefreshToken(RefreshToken $token): ?User {
        $qb = $this->connection->createQueryBuilder();
        $qb->addSelect("id")
                ->addSelect("email")
                ->addSelect("firstname")
                ->addSelect("lastname")
                ->addSelect("password")
                ->addSelect("users.token")
                ->addSelect("tokendate")
                ->addSelect("resettoken")
                ->addSelect("resetdate");
        $qb->from("users")
                ->innerJoin('users', 'apptoken', 'apptoken', 'apptoken.userid=users.id');
        $qb->where("id= {$qb->createNamedParameter($token->getId())}")
                ->andWhere("apptoken.token={$qb->createNamedParameter($token->getToken())}");

        $stmt = $qb->executeQuery();
        $row = $stmt->fetchAssociative();

        if (!$row) {
            return null;
        }

        return User::createFromRow($row);
    }
}
