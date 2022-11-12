<?php

declare (strict_types=1);

namespace trainingAPI\Login;

use DateInterval;
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
    private $recordedEvents = [];

    public function __construct(int $id, string $email, string $firstname, string $lastname, string $password,
            string $token, DateTimeImmutable $tokenDate, ?string $resetToken = null, ?DateTimeImmutable $resetDate = null) {
        $this->id = $id;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->password = $password;
        $this->token = $token;
        $this->tokenDate = $tokenDate;
        if ($resetToken) {
            $this->resetToken = $resetToken;
            $this->resetDate = $resetDate;
        }
    }

    public static function register(string $email, string $firstname, string $lastname, string $password): User {
        return new User(-1, $email, $firstname, $lastname, password_hash($password, PASSWORD_DEFAULT), bin2hex(random_bytes(10)), new DateTimeImmutable());
    }

    public static function createFromRow(array $row): User {
        return new User((int) $row["id"], $row["email"], $row["firstname"], $row["lastname"], $row["password"],
                $row["token"], new DateTimeImmutable($row["tokendate"]), $row["resettoken"] ?? null, is_null($row["resetdate"]) ? null : new DateTimeImmutable($row["resetdate"]) ?? null);
    }

    public function logIn(string $password): void {
        if (!password_verify($password, $this->password)) {
            
            return;
        }

        $this->recordedEvents[] = new UserWasLoggedIn();
    }

    public function forgot(): void {
        $this->resetToken = bin2hex(random_bytes(10));
        $expires = new DateTimeImmutable();
        $nyttDatum = $expires->add(new DateInterval('P1D'));  // Ett dygn!
        $this->resetDate = $nyttDatum;
    }

    public function changePassword(string $password): void {
        $this->setPassword($password);
        $this->setResetToken(null);
        $this->setResetDate(null);
        $this->setToken(bin2hex(random_bytes(10)));
        $this->setTokenDate(new DateTimeImmutable());
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

    public function getTokenDate(): ?DateTimeImmutable {
        return $this->tokenDate;
    }

    public function getResetToken(): ?string {
        return $this->resetToken;
    }

    public function getResetDate(): ?DateTimeImmutable {
        return $this->resetDate;
    }

    public function getRecordedEvents(): array {
        return $this->recordedEvents;
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

    public function setTokenDate(?DateTimeImmutable $tokenDate): void {
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
        $me->tokenDate = $this->getTokenDate() === null ? "" : $this->getTokenDate()->format("Y-m-d H:i") ?? "";
        // Password, ResetToken och ResetDate ska inte skickas till klienten.
        return $me;
    }

}
