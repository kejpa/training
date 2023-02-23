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

    public function __construct(private string $queryId, private string $id, private string $date, private string $length, 
            private string $description, private ?int $rpe, private array $validators) {
        
    }

    public function hasValidationErrors(): bool {
        $return = ($this->queryId !== $this->id);
        foreach ($this->validators as $key => $validator) {
            if (!$validator->validate($this->$key)) {
                $return = true;
            }
        }

        return $return;
    }

    public function getValidationErrors(): array {
        $errors = [];
        foreach ($this->validators as $key => $validator) {
            $errors = array_merge($errors, $validator->getErrors());
        }
        if ($this->queryId !== $this->id) {
            $errors[] = "Bodyid and query id, don't match";
        }
        return $errors;
    }

    public function toCommand(int $userId): Session {
        return new Session($this->id === "" ? -1 : (int) $this->id, $userId, (int) $this->length, new DateTimeImmutable($this->date), $this->description, $this->rpe);
    }

}
