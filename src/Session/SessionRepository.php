<?php

namespace trainingAPI\Session;

/**
 *
 * @author kjell
 */
interface SessionRepository {
    public function getAllSessions(int $userid):array;
    public function getAllSession(int $userid, int $sessionId):?Session;
}
