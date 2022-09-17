<?php

declare (strict_types=1);

namespace trainingAPI\Session;

use DateTimeImmutable;

/**
 * Description of SessionForm
 *
 * @author kjell
 */
final class SessionForm {

    private $id;
    private $date;
    private $length;
    private $description;
    private $validators;

    public function __construct(string $id, string $date, string $length, string $description, array $validators) {
        $this->id = $id;
        $this->date = $date;
        $this->length = $length;
        $this->description = $description;
        $this->validators = $validators;
    }

    public function hasValidationErrors(): bool {
        foreach ($this->validators as $key => $validator) {
            if (!$validator->validate($this->$key)) {
                return true;
            }
        }
        return false;
    }

    public function getValidationErrors(): array {
        $errors = [];
        foreach ($this->validators as $key => $validator) {
            $errors = array_merge($errors, $validator->getErrors());
        }

        return $errors;
    }

    public function toCommand(int $userId): Session {
        return new Session($this->id===""? -1:(int) $this->id , $userId, (int) $this->length, new DateTimeImmutable($this->date), $this->description);
    }

}
