<?php

declare (strict_types=1);

namespace tests\JsonDB;

use trainingAPI\Session\SessionIdExists;

/**
 * Description of JsonSessionIdExists
 *
 * @author kjell
 */
final class JsonSessionIdExists implements SessionIdExists {

    public function __construct() {
        $json = file_get_contents(__DIR__ . "/sessions.json");
        $this->db = json_decode($json);
    }

    public function execute(int $id, int $userId): bool {
        foreach ($this->db as $value) {
            if ($value->id === $id && $value->userid === $userId) {
                return true;
            }
        }
        return false;
    }

}
