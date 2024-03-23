<?php

declare (strict_types=1);

namespace trainingAPI\Framework\ChainOfResponse\Validator;

use Exception;
use trainingAPI\Framework\ChainOfResponse\ChainOfResponseValidator;

/**
 * IdIsValidValidator
 * Kontrollerar om angivet id är ett heltal
 * @author kjell
 */
final class IdIsValidValidator extends ChainOfResponseValidator {

    public function check(string $id): bool {
        try {
            $value = filter_var($id, FILTER_VALIDATE_INT);
            if ($value === false) {
                $this->appendError([__CLASS__, "Ogiltigt angivet id ($id)"]);
                return false;
            }
        } catch (Exception $e) {
            $this->appendError([__CLASS__, $e->getMessage()]);
            return false;
        }

        return true;
    }
}
