<?php

declare (strict_types=1);

namespace trainingAPI\Jwt;

use Doctrine\DBAL\Connection;

/**
 * Description of DbalTokenRepository
 *
 * @author kjell
 */
final class DbalTokenRepository implements TokenRepository {

    public function __construct(private Connection $connection) {
        $qb = $this->connection->createQueryBuilder();

        // Radera gamla refreshtokens
        $qb->delete('apptoken')
                ->where("expires<{$qb->createNamedParameter(date('Y-m-d H:i'))}");

        $qb->executeStatement();
    }

    public function addRefreshToken(RefreshToken $token): void {
        $qb = $this->connection->createQueryBuilder();

        $qb->insert('apptoken');
        $qb->values(['userid' => $qb->createNamedParameter($token->getId()),
            'token' => $qb->createNamedParameter($token->getToken()),
            'expires' => $qb->createNamedParameter(date('Y-m-d H:i', $token->getExpires()))
        ]);

        $qb->executeStatement();
    }

    public function removeRefreshToken(RefreshToken $token): void {
        $qb = $this->connection->createQueryBuilder();

        $qb->delete('apptoken');
        $qb->where("userid ={$qb->createNamedParameter($token->getId())}")
                ->andWhere("token={$qb->createNamedParameter($token->getToken())}");

        $qb->executeStatement();
    }

    public function updateRefreshToken(RefreshToken $token): void {
        $qb = $this->connection->createQueryBuilder();

        $qb->update('apptoken');
        $qb->set('expires', $qb->createNamedParameter(date('Y-m-d H:i', $token->getExpires())));
        $qb->where("userid ={$qb->createNamedParameter($token->getId())}")
                ->andWhere("token={$qb->createNamedParameter($token->getToken())}");

        $qb->executeStatement();
    }
}
