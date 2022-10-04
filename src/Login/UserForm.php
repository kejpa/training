<?php

declare (strict_types=1);

namespace trainingAPI\Login;

use DateTimeImmutable;

/**
 * Description of UserForm
 *
 * @author kjell
 */
final class UserForm {

    private $id;
    private $email;
    private $firstname;
    private $lastname;
    private $password;
    private $validators;

    public function __construct(?string $id, string $email, string $firstname, string $lastname, string $password, array $validators) {
        $this->id = $id;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->password = $password;
        $this->validators = $validators;
    }

    public static function fromRequest(array $request, array $validators = []): UserForm {
        return new UserForm($request["id"] ?? null, $request["email"], $request["firstname"], $request["lastname"], $request["password"], $validators);
    }
    public function getId(): ?string {
        return $this->id;
    }

        public function getEmail(): string {
        return $this->email;
    }

    public function getFirstname(): string {
        return $this->firstname;
    }

    public function getLastname(): string {
        return $this->lastname;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function hasValidationErrors(): bool {
        $result=false;
        foreach ($this->validators as $key => $validator) {
            if (!$validator->validate($this->$key)) {
                $result= true;
            }
        }

        return $result;
    }

    public function getValidationErrors(): array {
        $errors = [];
        foreach ($this->validators as $key => $validator) {
            $errors = array_merge($errors, $validator->getErrors());
        }

        return $errors;
    }

    public function toCommand(): User {
        if ($this->id === null) {
            return new User(-1, $this->email, $this->firstname, $this->lastname, $this->password, bin2hex(random_bytes(10)), new DateTimeImmutable());
        }
    }

}
