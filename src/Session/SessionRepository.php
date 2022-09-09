<?php

namespace trainingAPI\Session;

/**
 *
 * @author kjell
 */
interface SessionRepository {
    public function getAllSessions(int $userid);
}
