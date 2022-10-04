<?php

declare (strict_types=1);

namespace tests\JsonDB;

use trainingAPI\Login\EmailExistsQuery;

/**
 * Description of JsonEmailExistsQuery
 *
 * @author kjell
 */
final class JsonEmailExistsQuery implements EmailExistsQuery {

    private $db;

    public function __construct() {
        $json = file_get_contents(__DIR__ . "/users.json");
        $this->db = json_decode($json);
    }

    public function execute(string $email): bool {
        foreach ($this->db as $rec) {
            if($rec->email===$email) {
                return true;
            }
        }
        
        return false;
    }

}
