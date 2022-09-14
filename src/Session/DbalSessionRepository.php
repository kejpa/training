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

    public function getSession(int $userid, int $sessionId):?Session {
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

    public function addSession(int $userid, Session $session): int {
                $qb= $this->connection->createQueryBuilder();
        $qb->insert('sessions');
        $qb->values([
            "length"=>$qb->createNamedParameter($session->getLength()), 
            "date"=>$qb->createNamedParameter($session->getDate()->format("Y-m-d")),
            "description"=>$qb->createNamedParameter($session->getDescription()),
            "userid"=>$qb->createNamedParameter($session->getUserid()),
                ]);
        
        
            $stmt = $qb->execute();
            return (int) $this->connection->lastInsertId();
        
    }

    public function deleteSession(int $userid, Session $session): bool {
        
    }

    public function updateSession(int $userid, Session $session): bool {
        
    }

}
