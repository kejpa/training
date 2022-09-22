<?php
declare (strict_types=1);

namespace trainingAPI\Session;

use trainingAPI\Framework\ChainOfResponse\ChainOfResponseValidator;
use trainingAPI\Framework\ChainOfResponse\Validator\DateIsInPastValidator;
use trainingAPI\Framework\ChainOfResponse\Validator\DateIsValidValidator;
use trainingAPI\Framework\ChainOfResponse\Validator\IdExistsValidator;
use trainingAPI\Framework\ChainOfResponse\Validator\IdIsValidValidator;


/**
 * Description of sessionValidatorFactory
 *
 * @author kjell
 */
final class SessionValidatorFactory {

    public static function createSessionDateValidator(): ChainOfResponseValidator {
        $dateIsValidValidator = new DateIsValidValidator();
        $dateIsInPastValidator = new DateIsInPastValidator();
        $dateIsValidValidator->next($dateIsInPastValidator);

        return $dateIsValidValidator;
    }

    public static function createSessionIdValidator(int $userId, IdExistsValidator $idExistsValidator): ChainOfResponseValidator {
        $idIsValidValidator = new IdIsValidValidator();
        $idExistsValidator->setUserId($userId);
        $idIsValidValidator->next($idExistsValidator);

        return $idIsValidValidator;
    }

}
