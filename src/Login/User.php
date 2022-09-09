<?php

declare (strict_types=1);

namespace trainingAPI\Login;

use DateTimeImmutable;
use JsonSerializable;
use stdClass;

/**
 * Description of User
 *
 * @author kjell
 */
final class User implements JsonSerializable {

    private $id;
    private $email;
    private $firstname;
    private $lastname;
    private $password;
    private $token;
    private $tokenDate;
    private $resetToken;
    private $resetDate;

    public function __construct(int $id, string $email, string $firstname, string $lastname, string $password,
            string $token, DateTimeImmutable $tokenDate, ?string $resetToken = null, ?DateTimeImmutable $resetDate = null) {
        $this->id = $id;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->token = $token;
        $this->tokenDate = $tokenDate;
        if ($resetToken) {
            $this->resetToken = $resetToken;
            $this->resetDate = $resetDate;
        }
    }

    public static function register(string $email, string $firstname, string $lastname, string $password): User {
        return new User(-1, $email, $firstname, $lastname, $password, bin2hex(random_bytes(10)), new DateTimeImmutable(),
                bin2hex(random_bytes(10)), new DateTimeImmutable());
    }

    public static function createFromRow(array $row): User {
        return new User((int) $row["id"], $row["email"], $row["firstname"], $row["lastname"], $row["password"],
                $row["token"], new DateTimeImmutable($row["tokendate"]), $row["resettoken"] ?? null, is_null($row["resetdate"]) ? null : new DateTimeImmutable($row["resetdate"]) ?? null);
    }

    public function getId(): int {
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

    public function getToken(): string {
        return $this->token;
    }

    public function getTokenDate(): \DateTimeImmutable {
        return $this->tokenDate;
    }

    public function getResetToken(): string {
        if (!is_null($this->resetToken)) {
            return $this->resetToken;
        } else {
            return "";
        }
    }

    public function getResetDate(): ?\DateTimeImmutable {
        return $this->resetDate;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setFirstname(string $firstname): void {
        $this->firstname = $firstname;
    }

    public function setLastname(string $lastname): void {
        $this->lastname = $lastname;
    }

    public function setPassword(string $password): void {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function setToken(string $token): void {
        $this->token = $token;
    }

    public function setTokenDate(\DateTimeImmutable $tokenDate): void {
        $this->tokenDate = $tokenDate;
    }

    public function setResetToken(?string $resetToken): void {
        $this->resetToken = $resetToken;
    }

    public function setResetDate(?\DateTimeImmutable $resetDate): void {
        $this->resetDate = $resetDate;
    }

    public function jsonSerialize(): stdClass {
        $me = new stdClass();
        $me->id = $this->getId();
        $me->email = $this->getEmail();
        $me->firstname = $this->getFirstname();
        $me->lastname = $this->getLastname();
        $me->token = $this->getToken();
        $me->tokenDate = $this->getTokenDate()->format("Y-m-d H:i");
        if (!is_null($this->resetToken)) {
            $me->resetToken = $this->getResetToken();
        }
        if (!is_null($this->resetDate)) {
            $me->resetDate = $this->getResetDate();
        }

        return $me;
    }

}
