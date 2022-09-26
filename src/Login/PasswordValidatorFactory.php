<?php
declare (strict_types=1);

namespace trainingAPI\Login;

use trainingAPI\Framework\ChainOfResponse\ChainOfResponseValidator;
use trainingAPI\Framework\ChainOfResponse\Validator\PasswordContainsValidCharactersValidator;
use trainingAPI\Framework\ChainOfResponse\Validator\PasswordIsLongEnoughValidator;


/**
 * Description of PasswordValidatorFactory
 *
 * @author kjell
 */
final class PasswordValidatorFactory {

    public static function createPasswordValidator(): ChainOfResponseValidator {
        $passwordContainsValidCharactersValidator = new PasswordContainsValidCharactersValidator();
        $passwordIsLongEnoughValidator = new PasswordIsLongEnoughValidator();
        $passwordContainsValidCharactersValidator->next($passwordIsLongEnoughValidator);

        return $passwordContainsValidCharactersValidator;
    }

}
