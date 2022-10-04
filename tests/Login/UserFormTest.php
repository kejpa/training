<?php

declare (strict_types=1);
namespace tests\Login;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use trainingAPI\Login\UserForm;

/**
 * Description of UserFormTest
 *
 * @author kjell
 */
final class UserFormTest extends TestCase {
    public function testFromRequest() {
        $request=new Request([], ["id"=>"1", "email"=>"email", "firstname"=>"first", 
            "lastname"=>"last", "password"=>"password"]);
        $test= UserForm::fromRequest($request->request->all());
        
        $this->assertEquals(1, $test->getId());
        $this->assertEquals("first", $test->getFirstname());
        $this->assertEquals("last", $test->getLastname());
        $this->assertEquals("password", $test->getPassword());

        $request=new Request([], ["email"=>"email", "firstname"=>"first", 
            "lastname"=>"last", "password"=>"password"]);
        $test= UserForm::fromRequest($request->request->all());
        
        $this->assertNull( $test->getId());
    }
    public function testToCommand() {
        $request=new Request([], ["email"=>"email", "firstname"=>"first", 
            "lastname"=>"last", "password"=>"password"]);
        $test= UserForm::fromRequest($request->request->all());
        $user=$test->toCommand();
        
        $this->assertEquals(-1, $user->getId());
        $this->assertEquals("first", $user->getFirstname());
        $this->assertEquals("last", $user->getLastname());
        $this->assertEquals("password", $user->getPassword());
        $this->assertEquals(date("Y-m-d"), $user->getTokenDate()->format("Y-m-d"));
    }
}
