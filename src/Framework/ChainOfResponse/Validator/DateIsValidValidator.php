<?php

declare (strict_types=1);

namespace trainingAPI\Framework\ChainOfResponse\Validator;

use DateTimeImmutable;
use Exception;
use trainingAPI\Framework\ChainOfResponse\ChainOfResponseValidator;

/**
 * FileExistsValidator
 * Kontrollerar om angivet datum är ett giltigt datum 
 * @author kjell
 */
final class DateIsValidValidator extends ChainOfResponseValidator {

    public function check(string $date): bool {
        try {
            $datum = new DateTimeImmutable($date);
            if ($datum->format("Y-m-d") !== $date) {
                $this->appendError([__CLASS__, "Ogiltigt datum ($date)"]);
                return false;
            }
        } catch (Exception $e) {
            $this->appendError([__CLASS__, $e->getMessage()]);
            return false;
        }

        return true;
    }
}
