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

    private $idExistsValidator;

    public function __construct(IdExistsValidator $idExistsValidator) {
        $this->idExistsValidator = $idExistsValidator;
    }

    public  function createSessionDateValidator(): ChainOfResponseValidator {
        $dateIsValidValidator = new DateIsValidValidator();
        $dateIsInPastValidator = new DateIsInPastValidator();
        $dateIsValidValidator->next($dateIsInPastValidator);

        return $dateIsValidValidator;
    }

    public  function createSessionIdValidator(int $userId): ChainOfResponseValidator {
        $idIsValidValidator = new IdIsValidValidator();
        $idExistsValidator = $this->idExistsValidator;
        $idExistsValidator->setUserId($userId);
        $idIsValidValidator->next($idExistsValidator);

        return $idIsValidValidator;
    }

}
