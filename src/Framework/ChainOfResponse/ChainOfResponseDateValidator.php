<?php

declare (strict_types=1);

namespace trainingAPI\Framework\ChainOfResponse;

/**
 * Abstrakt klass för Chain of Command för Lenex-fil validering
 * @author kjell
 */
abstract class ChainOfResponseDateValidator implements ChainOfResponseValidator {

    private $next;
    private $errors = [];

    public function validate(string $date): bool {
        if ($this->next && $this->check($date)) {
            if (!$this->next->validate($date)) {
                $this->errors = array_merge($this->errors, $this->next->getErrors());
                return false;
            }
            return true;
        }
        return $this->check($date);
    }

    abstract public function check(string $date): bool;

    public function appendError($error): void {
        if (is_array($error)) {
            $this->errors = array_merge($this->errors, $error);
        } else {
            $this->errors[] = $error;
        }
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function next(ChainOfResponseValidator $validator): void {
        $this->next = $validator;
    }

}
