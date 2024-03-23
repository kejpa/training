<?php

declare (strict_types=1);

namespace trainingAPI\Framework\ChainOfResponse\Validator;

use Exception;
use trainingAPI\Framework\ChainOfResponse\ChainOfResponseValidator;
use function mb_strlen;

/**
 * PasswordContainsValidCharactersValidator
 * Kontrollerar om inmatat lösenord innehåller otillåtna tecken (lägre än ASCII32)
 * @author kjell
 */
final class PasswordIsLongEnoughValidator extends ChainOfResponseValidator {

    public function check(string $password): bool {
        try {
            if (mb_strlen($password) < 10) {
                $this->appendError([__CLASS__, "Lösenord ska vara minst 10 tecken"]);
                return false;
            }
        } catch (Exception $exc) {
            $this->appendError([__CLASS__, $exc->getMessage()]);
            return false;
        }

        return true;
    }
}
