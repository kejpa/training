<?php

declare (strict_types=1);

namespace trainingAPI\Session;

use Doctrine\DBAL\Connection;

/**
 * Description of DbalSessionRepository
 *
 * @author kjell
 */
final class DbalSessionRepository implements SessionRepository {

    private $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function getAllSessions(int $userid): array {
        $qb = $this->connection->createQueryBuilder();
        $qb->addSelect("id")
                ->addSelect("userid")
                ->addSelect("length")
                ->addSelect("date")
                ->addSelect("description");
        $qb->from("sessions");
        $qb->where('userid=' . $qb->createNamedParameter($userid));

        $stmt = $qb->execute();
        $rows = $stmt->fetchAll();
        $sessions = [];
        foreach ($rows as $row) {
            $sessions[] = Session::createFromRow($row);
        }

        return $sessions;
    }

    public function getAllSession(int $userid, int $sessionId): ?Session {
        $qb = $this->connection->createQueryBuilder();
        $qb->addSelect("id")
                ->addSelect("userid")
                ->addSelect("length")
                ->addSelect("date")
                ->addSelect("description");
        $qb->from("sessions");
        $qb->where('userid=' . $qb->createNamedParameter($userid))
                ->andWhere('id=' . $qb->createNamedParameter($sessionId));

        $stmt = $qb->execute();
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        $session = Session::createFromRow($row);

        return $session;
    }

}
