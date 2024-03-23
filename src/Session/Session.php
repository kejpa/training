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

    public function __construct(private int $id, private int $userid, private int $length, private DateTimeImmutable $date,
            private string $description, private ?int $rpe = null) {
        
    }

    public static function createFromRow(array $row): ?Session {
        return new Session((int) $row["id"], (int) $row["userid"], (int) $row["length"],
                new DateTimeImmutable($row["date"]), $row["description"], $row["rpe"]);
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

    public function getRpe(): int {
        return $this->rpe;
    }

    public function setRpe(int $rpe): void {
        $this->rpe = $rpe;
    }

    public function jsonSerialize(): stdClass {
        $me = new stdClass();
        $me->id = $this->id;
        $me->userid = $this->userid;
        $me->length = $this->length;
        $me->date = $this->date->format("Y-m-d");
        $me->description = $this->description;
        $me->rpe = $this->rpe;

        return $me;
    }
}
