<?php

declare (strict_types=1);

namespace tests\Session;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use trainingAPI\Session\Session;

/**
 * Description of SessionTest
 *
 * @author kjell
 */
final class SessionTest extends TestCase {

    public function testProperties() {
        $test = new Session(1, 2, 1000, new DateTimeImmutable("2022-02-02"), "description");

        $this->assertEquals(1, $test->getId());
        $this->assertEquals(2, $test->getUserid());
        $this->assertEquals(1000, $test->getLength());
        $this->assertEquals("2022-02-02", $test->getDate()->format("Y-m-d"));
        $this->assertEquals("description", $test->getDescription());

        $test->setId(2);
        $test->setUserid(3);
        $test->setLength(500);
        $test->setDate(new DateTimeImmutable("2022-03-03"));
        $test->setDescription("Testing testing");

        $this->assertEquals(2, $test->getId());
        $this->assertEquals(3, $test->getUserid());
        $this->assertEquals(500, $test->getLength());
        $this->assertEquals("2022-03-03", $test->getDate()->format("Y-m-d"));
        $this->assertEquals("Testing testing", $test->getDescription());
    }

    public function testCreateFromRow() {
        $row = ['id' => 1, "userid" => 2, "date" => "2022-02-02", "length" => 1000, "description" => "Testing testing"];
        $test = Session::createFromRow($row);

        $this->assertEquals(1, $test->getId());
        $this->assertEquals(2, $test->getUserid());
        $this->assertEquals(1000, $test->getLength());
        $this->assertEquals("2022-02-02", $test->getDate()->format("Y-m-d"));
        $this->assertEquals("Testing testing", $test->getDescription());
    }

}
