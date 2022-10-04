<?php

declare (strict_types=1);

namespace tests\Login;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use trainingAPI\Login\User;

/**
 * Description of UserTest
 *
 * @author kjell
 */
final class UserTest extends TestCase {

    public function testProperties() {
        $test = new User(1, "epost", "first", "last", "password", "token", new DateTimeImmutable());
        $this->assertEquals(1, $test->getId());
        $this->assertEquals("first", $test->getFirstname());
        $this->assertEquals("last", $test->getLastname());
        $this->assertEquals("password", $test->getPassword());
        $this->assertEquals("token", $test->getToken());
        $this->assertEquals(date("Y-m-d"), $test->getTokenDate()->format("Y-m-d"));

        $test = new User(1, "epost", "first", "last", "password", "token", new DateTimeImmutable(), "reset", new DateTimeImmutable("yesterday"));
        $this->assertEquals("reset", $test->getResetToken());
        $this->assertEquals(date("Y-m-d", strtotime("yesterday")), $test->getResetDate()->format("Y-m-d"));
    }

    public function testRegister() {
        $test = User::register("email", "first", "last", "password");
        $this->assertEquals(-1, $test->getId());
        $this->assertEquals("first", $test->getFirstname());
        $this->assertEquals("last", $test->getLastname());
        $this->assertTrue(password_verify("password", $test->getPassword()));
        $this->assertEquals(date("Y-m-d"), $test->getTokenDate()->format("Y-m-d"));
    }
    public function testCreateFromRow() {
        $test=User::createFromRow(["id"=>1, "email"=>"email", "firstname"=>"first", 
            "lastname"=>"last", "password"=>"password", "token"=>"token", 
            "tokendate"=>date("Y-m-d"), "resettoken"=>null, "resetdate"=>null]);
        $this->assertEquals(1, $test->getId());
        $this->assertEquals("first", $test->getFirstname());
        $this->assertEquals("last", $test->getLastname());
        $this->assertEquals("password", $test->getPassword());
        $this->assertEquals("token", $test->getToken());
        $this->assertEquals(date("Y-m-d"), $test->getTokenDate()->format("Y-m-d"));
        $this->assertNull($test->getResetToken());
        $this->assertNull($test->getResetDate());

        $test=User::createFromRow(["id"=>1, "email"=>"email", "firstname"=>"first", 
            "lastname"=>"last", "password"=>"password", "token"=>"token", 
            "tokendate"=>date("Y-m-d"), "resettoken"=>"reset", "resetdate"=>date("Y-m-d")]);
        $this->assertEquals("reset", $test->getResetToken());
        $this->assertEquals(date("Y-m-d"), $test->getResetDate()->format("Y-m-d"));
    }
    
    public function testLogin() {
        $test = User::register("email", "first", "last", "password");
        $test->logIn("password");
        
        $this->assertEquals(1, count($test->getRecordedEvents()));

        $test = User::register("email", "first", "last", "password");
        $test->logIn("wrong");
        
        $this->assertEquals(0, count($test->getRecordedEvents()));
    }
    
    public function testForgot() {
        $test = User::register("email", "first", "last", "password");
        $this->assertNull($test->getResetToken());
        
        $test->forgot();
        $this->assertNotNull($test->getResetToken());
        $this->assertEquals(date("Y-m-d", strtotime("tomorrow")), $test->getResetDate()->format("Y-m-d"));
    }
    public function testChangePassword() {
        $test = User::register("email", "first", "last", "password");
        $this->assertNull($test->getResetToken());
        
        $test->forgot();
        $this->assertNotNull($test->getResetToken());
        $test->changePassword("password");
        $this->assertNull($test->getResetToken());
        $this->assertNull($test->getResetDate());
        
    }
}
