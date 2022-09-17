<?php

declare (strict_types=1);

namespace trainingAPI\Framework\ChainOfResponse\Validator;

use DateTimeImmutable;
use Exception;
use trainingAPI\Framework\ChainOfResponse\ChainOfResponseValidator;

/**
 * DateIsInPastValidator
 * Kontrollerar om inmatat datum är tidigare än dagens datum
 * @author kjell
 */
final class DateIsInPastValidator extends ChainOfResponseValidator  {

    public function check(string $date): bool {
        try {
            $datum = new DateTimeImmutable($date);
            $idag = new DateTimeImmutable();
            if ($datum->getTimestamp() > $idag->getTimestamp()) {
                $this->appendError([__CLASS__, "Datum får inte vara framåt i tiden ($date)"]);
                return false;
            }
        } catch (Exception $exc) {
            $this->appendError([__CLASS__, $exc->getMessage()]);
            return false;
        }

        return true;
    }

}
