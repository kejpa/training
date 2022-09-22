<?php

declare (strict_types=1);

namespace trainingAPI\Framework\ChainOfResponse;

/**
 * Abstrakt klass för Chain of Command för Lenex-fil validering
 * @author kjell
 */
abstract class ChainOfResponseValidator implements ChainOfResponse {

    private $next;
    private $errors = [];

     public function validate(string $check): bool {
        if ($this->next && $this->check($check)) {
            if (!$this->next->validate($check)) {
                $this->errors = array_merge($this->errors, $this->next->getErrors());
                return false;
            }
            return true;
        }
        return $this->check($check);
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

    final public function next(ChainOfResponse $validator): void {
        $this->next = $validator;
    }

}
