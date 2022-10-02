<?php

declare (strict_types=1);

namespace trainingAPI\Login;

use trainingAPI\Framework\ChainOfResponse\ChainOfResponseValidator;
use trainingAPI\Framework\ChainOfResponse\Validator\EmailDontExistsValidator;
use trainingAPI\Framework\ChainOfResponse\Validator\EmailIsValidValidator;

/**
 * Description of PasswordValidatorFactory
 *
 * @author kjell
 */
final class EmailValidatorFactory {

    public static function createEmailDontExistsValidator(EmailExistsQuery $emailExistsQuery): ChainOfResponseValidator {
        $emailIsValid = new EmailIsValidValidator();
        $emailDontExists=new EmailDontExistsValidator($emailExistsQuery);
        $emailIsValid->next($emailDontExists);

        return $emailIsValid;
    }

}
