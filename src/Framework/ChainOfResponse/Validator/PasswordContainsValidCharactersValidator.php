<?php

declare (strict_types=1);

namespace trainingAPI\Framework\ChainOfResponse\Validator;

use Exception;
use trainingAPI\Framework\ChainOfResponse\ChainOfResponseValidator;

/**
 * PasswordContainsValidCharactersValidator
 * Kontrollerar om inmatat lösenord innehåller otillåtna tecken (lägre än ASCII32)
 * @author kjell
 */
final class PasswordContainsValidCharactersValidator extends ChainOfResponseValidator {

    public function check(string $password): bool {
        try {
            $pwd = filter_var($password, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
            if ($pwd !== $password) {
                $this->appendError([__CLASS__, "Lösenord innehåller otillåtna tecken($pwd!==$password)"]);
                return false;
            }
        } catch (Exception $exc) {
            $this->appendError([__CLASS__, $exc->getMessage()]);
            return false;
        }

        return true;
    }
}
