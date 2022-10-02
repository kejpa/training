<?php

declare (strict_types=1);

namespace trainingAPI\Framework\ChainOfResponse\Validator;

use Exception;
use trainingAPI\Framework\ChainOfResponse\ChainOfResponseValidator;

/**
 * EmailIsValidValidator
 * Kontrollerar om angiven epostadress är en giltig adress
 * @author kjell
 */
final class EmailIsValidValidator extends ChainOfResponseValidator{

    public function check(string $email): bool {
        try {
            $test = filter_var($email, FILTER_VALIDATE_EMAIL);
            if ($test !== $email) {
                $this->appendError([__CLASS__ ,"Ogiltig epostadress"]);
                return false;
            }
        } catch (Exception $e) {
            $this->appendError([__CLASS__ ,$e->getMessage()]);
            return false;
        }

        return true;
    }

}
