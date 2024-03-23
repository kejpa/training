<?php

declare (strict_types=1);

namespace trainingAPI\Framework\ChainOfResponse\Validator;

use Exception;
use trainingAPI\Framework\ChainOfResponse\ChainOfResponseValidator;
use trainingAPI\Session\SessionIdExists;

/**
 * IdIsValidValidator
 * Kontrollerar om angivet id existerar i tabellen sessions
 * @author kjell
 */
final class IdExistsValidator extends ChainOfResponseValidator {

    private $sessionIdExists;
    private $userId;

    public function __construct(SessionIdExists $sessionIdExists) {
        $this->sessionIdExists = $sessionIdExists;
    }

    public function setUserId($userId): void {
        $this->userId = $userId;
    }

    public function check(string $id): bool {
        try {
            $value = $this->sessionIdExists->execute((int) $id, $this->userId);
            if ($value === false) {
                $this->appendError([__CLASS__, "Ogiltigt angivet id saknas i databasen ($id)"]);
                return false;
            }
        } catch (Exception $e) {
            $this->appendError([__CLASS__, $e->getMessage()]);
            return false;
        }

        return true;
    }
}
