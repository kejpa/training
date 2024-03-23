<?php

namespace trainingAPI\Session;

/**
 *
 * @author kjell
 */
interface SessionIdExists {

    public function execute(int $id, int $userId): bool;
}
