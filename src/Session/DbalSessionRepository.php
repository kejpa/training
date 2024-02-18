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
                ->addSelect("rpe")
                ->addSelect("description");
        $qb->from("sessions");
        $qb->where('userid=' . $qb->createNamedParameter($userid));
        $qb->orderBy("date","DESC");

        $stmt = $qb->executeQuery();
        $rows = $stmt->fetchAllAssociative();
        $sessions = [];
        foreach ($rows as $row) {
            $sessions[] = Session::createFromRow($row);
        }

        return $sessions;
    }

    public function getSession(int $userid, int $sessionId): ?Session {
        $qb = $this->connection->createQueryBuilder();
        $qb->addSelect("id")
                ->addSelect("userid")
                ->addSelect("length")
                ->addSelect("date")
                ->addSelect("rpe")
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
        $qb = $this->connection->createQueryBuilder();
        $qb->insert('sessions');
        $qb->values([
            "length" => $qb->createNamedParameter($session->getLength()),
            "date" => $qb->createNamedParameter($session->getDate()->format("Y-m-d")),
            "description" => $qb->createNamedParameter($session->getDescription()),
            "userid" => $qb->createNamedParameter($session->getUserid()),
            "rpe" => $qb->createNamedParameter($session->getRpe()),
        ]);

        $qb->executeStatement();
        return (int) $this->connection->lastInsertId();
    }

    public function deleteSession(int $id, int $userId): int {
        $qb = $this->connection->createQueryBuilder();
        $qb->delete('sessions');
        $qb->where('id=' . $qb->createNamedParameter($id))
                ->andWhere('userid =' . $qb->createNamedParameter($userId));

        return $qb->executeStatement();
    }

    public function updateSession(int $userid, Session $session): int {
        $qb = $this->connection->createQueryBuilder();
        $qb->update('sessions');
        $qb->set('length', $qb->createNamedParameter($session->getLength()))
                ->set('date', $qb->createNamedParameter($session->getDate()->format("Y-m-d")))
                ->set('description', $qb->createNamedParameter($session->getDescription()))
                ->set('rpe', $qb->createNamedParameter($session->getRpe()));
        $qb->where('id=' . $qb->createNamedParameter($session->getId()))
                ->andWhere('userid =' . $qb->createNamedParameter($session->getUserid()));

        return $qb->executeStatement();
    }

}
