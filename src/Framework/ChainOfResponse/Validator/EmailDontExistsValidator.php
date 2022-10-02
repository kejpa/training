<?php

declare (strict_types=1);

namespace trainingAPI\Framework\ChainOfResponse\Validator;

use Exception;
use trainingAPI\Framework\ChainOfResponse\ChainOfResponseValidator;
use trainingAPI\Login\EmailExistsQuery;

/**
 * EmailDontExistsValidator
 * Kontrollerar att angiven emailadress inte existerar i tabellen users
 * @author kjell
 */
final class EmailDontExistsValidator extends ChainOfResponseValidator {

    private $emailExists;

    public function __construct(EmailExistsQuery $emailExists) {
        $this->emailExists = $emailExists;
    }

    public function check(string $email): bool {
        try {
            $value = $this->emailExists->execute($email);
            if ($value === true) {
                $this->appendError([__CLASS__, "Angiven epostadress finns redan i databasen ($email)"]);
                return false;
            }
        } catch (Exception $e) {
            $this->appendError([__CLASS__, $e->getMessage()]);
            return false;
        }

        return true;
    }

}
