<?php

declare (strict_types=1);

namespace trainingAPI\Session;

use trainingAPI\Framework\ChainOfResponse\ChainOfResponseDateValidator;
use trainingAPI\Framework\ChainOfResponse\Validator\DateIsInPastValidator;
use trainingAPI\Framework\ChainOfResponse\Validator\DateIsValidValidator;

/**
 * Description of sessionValidatorFactory
 *
 * @author kjell
 */
final class sessionValidatorFactory {

    public static function createSessionDateValidator(): ChainOfResponseDateValidator {
        $dateIsValidValidator = new DateIsValidValidator();
        $dateIsInPastValidator = new DateIsInPastValidator();
        $dateIsValidValidator->next($dateIsInPastValidator);

        return $dateIsValidValidator;
    }
}
