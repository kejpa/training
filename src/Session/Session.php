<?php

declare (strict_types=1);

namespace trainingAPI\Session;

use DateTimeImmutable;
use JsonSerializable;
use stdClass;

/**
 * Description of Session
 *
 * @author kjell
 */
final class Session implements JsonSerializable {

    private $id;
    private $userid;
    private $length;
    private $date;
    private $description;

    public function __construct(int $id, int $userid, int $length, DateTimeImmutable $date, string $description) {
        $this->id = $id;
        $this->userid = $userid;
        $this->length = $length;
        $this->date = $date;
        $this->description = $description;
    }

    public static function createFromRow(array $row): ?Session {
        return new Session((int) $row["id"], (int) $row["userid"], (int) $row["length"],
                new DateTimeImmutable($row["date"]), $row["description"]);
    }

    public function getId(): int {
        return $this->id;
    }

    public function getUserid(): int {
        return $this->userid;
    }

    public function getLength(): int {
        return $this->length;
    }

    public function getDate(): DateTimeImmutable {
        return $this->date;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setUserid(int $userid): void {
        $this->userid = $userid;
    }

    public function setLength(int $length): void {
        $this->length = $length;
    }

    public function setDate(DateTimeImmutable $date): void {
        $this->date = $date;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function jsonSerialize(): stdClass{
        $me = new stdClass();
        $me->id = $this->id;
        $me->userid = $this->userid;
        $me->length = $this->length;
        $me->date = $this->date->format("Y-m-d");
        $me->description = $this->description;

        return $me;
    }

}
