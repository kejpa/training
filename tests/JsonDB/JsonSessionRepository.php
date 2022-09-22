<?php

declare (strict_types=1);

namespace tests\JsonDB;

use DateTimeImmutable;
use trainingAPI\Session\Session;
use trainingAPI\Session\SessionRepository;

/**
 * Description of JsonSessionRepository
 *
 * @author kjell
 */
final class JsonSessionRepository implements SessionRepository {

    private $db;
    private $lastId;

    public function __construct() {
        $this->lastId = 0;
        $json = file_get_contents(__DIR__ . "/sessions.json");
        $this->db = json_decode($json);
        foreach ($this->db as $rec) {
            if ($rec->id > $this->lastId) {
                $this->lastId = $rec->id;
            }
        }
    }

    public function addSession(int $userid, Session $session): int {
        $this->lastId++;
        $session->setId($this->lastId);
        array_push($this->db, $session);

        return $session->getId();
    }

    public function deleteSession(int $id, int $userId): int {
        
    }

    public function getAllSessions(int $userid): array {
        $ret = [];
        foreach ($this->db as $rec) {
            if ($rec->userid === $userid) {
                array_push($ret, $rec);
            }
        }
        return $ret;
    }

    public function getSession(int $userid, int $sessionId): ?Session {
        foreach ($this->db as $rec) {
            if ($rec->userid === $userid) {
                return new Session($rec->id, $rec->userid, $rec->length,
                        new DateTimeImmutable($rec->date), $rec->description);
            }
        }
        return null;
    }

    public function updateSession(int $userid, Session $session): int {
        $antal = 0;
        $session->setUserid($userid);
        foreach ($this->db as $rec) {
            if ($rec->userid === $userid && $rec->id === $session->getId()) {
                $rec = $session;
                $antal++;
            }
        }

        return $antal;
    }

}
