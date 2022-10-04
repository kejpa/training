<?php

declare (strict_types=1);

namespace tests\Login;

use PHPUnit\Framework\TestCase;
use tests\JsonDB\JsonUserRepository;
use trainingAPI\Login\Login;
use trainingAPI\Login\LoginHandler;

/**
 * Description of LoginHandlerTest
 *
 * @author kjell
 */
final class LoginHandlerTest extends TestCase {

    private $userRepository;

    public function __construct() {
        parent::__construct();
        $this->userRepository=new JsonUserRepository();
        
    }
    public function testHandleNoUser() {
        $test=new LoginHandler($this->userRepository);
        $login=new Login("email", "password");
        $user=$test->handle($login);

        $this->assertNull($user);
    }
    public function testHandleUserBadPassword() {
        $test=new LoginHandler($this->userRepository);
        $login=new Login("kjell@kejpa.com", "password");
        $user=$test->handle($login);

        $this->assertEquals(0,count($user->getRecordedEvents()));
    }
    public function testHandleUserWasLoggedIn() {
        $test=new LoginHandler($this->userRepository);
        $login=new Login("kjell@kejpa.com", "pwd");
        $user=$test->handle($login);

        $this->assertEquals(1,count($user->getRecordedEvents()));
    }
}
