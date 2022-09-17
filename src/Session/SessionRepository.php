<?php

namespace trainingAPI\Session;

/**
 *
 * @author kjell
 */
interface SessionRepository {
    public function getAllSessions(int $userid):array;
    public function getSession(int $userid, int $sessionId):?Session;
    public function addSession(int $userid, Session $session):int;
    public function updateSession(int $userid, Session $session): int;
    public function deleteSession(int $id, int $userId): int;
}
