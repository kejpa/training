<?php

declare (strict_types=1);

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use tests\JsonDB\JsonEmailExistsQuery;
use tests\JsonDB\JsonUserRepository;
use trainingAPI\Login\LoginController;
use trainingAPI\Login\LoginHandler;

/**
 * Description of LoginControllerTest
 *
 * @author kjell
 */
final class LoginControllerTest extends TestCase {

    private $userRepository;
    private $loginHandler;
    private $emailExistsQuery;

    public function setUp(): void {
        $this->userRepository = new JsonUserRepository();
        $this->loginHandler = new LoginHandler($this->userRepository);
        $this->emailExistsQuery = new JsonEmailExistsQuery();
    }

    public function testGetUserByToken() {
        $test = new LoginController($this->userRepository, $this->loginHandler, $this->emailExistsQuery);
        $user = $test->getUserByToken("Fel");
        $this->assertNull($user);

        $user = $test->getUserByToken("Abcd1234");
        $this->assertEquals(1, $user->getId());
    }

    public function testLogIn() {
        $test = new LoginController($this->userRepository, $this->loginHandler, $this->emailExistsQuery);
        $request = new Request([], ["username" => "kjell", "password" => "fel"]);
        $this->assertEquals(405, $test->logIn($request)->getStatusCode());

        $request = new Request([], ["username" => "kjell@kejpa.com", "password" => "fel"]);
        $this->assertEquals(405, $test->logIn($request)->getStatusCode());

        $request = new Request([], ["username" => "kjell@kejpa.com", "password" => "pwd"]);
        $this->assertEquals(200, $test->logIn($request)->getStatusCode());
    }

    public function testResetPassword() {
        $test = new LoginController($this->userRepository, $this->loginHandler, $this->emailExistsQuery);
        $request = new Request();

        $this->assertEquals(400, $test->resetPassword($request, ["user" => "kjell"])->getStatusCode());
        $this->assertEquals(200, $test->resetPassword($request, ["user" => "kjell@kejpa.com"])->getStatusCode());
    }

    public function testChangePasswordBadUser() {
        $test = new LoginController($this->userRepository, $this->loginHandler, $this->emailExistsQuery);

        $request = new Request([], ["resetToken" => "fel", "password" => "password is long enough"]);
        $this->assertEquals(400, $test->changePassword($request, ["user" => "kjell"])->getStatusCode(), "Ingen användare");
    }

    public function testChangePasswordBadPassword() {
        $user = $this->userRepository->getUserByEmail("kjell@kejpa.com");
        $loginHandlerStub = $this->createStub(LoginHandler::class);
        $loginHandlerStub->method("handle")->willReturn($user);

        $test = new LoginController($this->userRepository, $loginHandlerStub, $this->emailExistsQuery);

        $request = new Request([], ["resetToken" => "fel", "password" => "password is long enough"]);
        $user->setResetToken("rätt");
        $this->assertEquals(400, $test->changePassword($request, ["user" => "kjell@kejpa.com"])->getStatusCode(), "ResetToken matchar inte");

        $request = new Request([], ["resetToken" => "rätt", "password" => "password is long enough"]);
        $user->setResetToken("rätt");
        $this->assertEquals(400, $test->changePassword($request, ["user" => "kjell@kejpa.com"])->getStatusCode(), "ResetDate är null");

        $request = new Request([], ["resetToken" => "rätt", "password" => "password is long enough"]);
        $user->setResetToken("rätt");
        $user->setResetDate(new DateTimeImmutable("last week"));
        $this->assertEquals(400, $test->changePassword($request, ["user" => "kjell@kejpa.com"])->getStatusCode(), "ResetDate är för gammalt");

        $user->forgot();

        $request = new Request([], ["resetToken" => $user->getResetToken(), "password" => "pwd"]);
        $this->assertEquals(403, $test->changePassword($request, ["user" => "kjell@kejpa.com"])->getStatusCode(), "för kort lösen");

        $request = new Request([], ["resetToken" => $user->getResetToken(), "password" => "password is long enough"]);
        $this->assertEquals(200, $test->changePassword($request, ["user" => "kjell@kejpa.com"])->getStatusCode(), "OK");
    }

    public function testUpdatePassword() {
        $test = new LoginController($this->userRepository, $this->loginHandler, $this->emailExistsQuery);

        $request = new Request([], ["password" => "Password is long enought"]);
        $request->headers->add(["user-token" => "Abcd1234"]);
        $this->assertEquals(405, $test->updatePassword($request, ["user" => "kjell"])->getStatusCode(), "Bad user");

        $request = new Request([], ["password" => "Password is long enought"]);
        $request->headers->add(["user-token" => "Fel"]);
        $this->assertEquals(405, $test->updatePassword($request, [])->getStatusCode(), "Bad user-token");

        $request = new Request([], ["password" => "Password is long enought"]);
        $request->headers->add(["user-token" => "Abcd1234"]);
        $this->assertEquals(405, $test->updatePassword($request, ["user" => "kjell.hansen@kejpa.com"])->getStatusCode(), "None-matching email");

        $request = new Request([], ["password" => "pwd"]);
        $request->headers->add(["user-token" => "Abcd1234"]);
        $this->assertEquals(403, $test->updatePassword($request, ["user" => "kjell@kejpa.com"])->getStatusCode(), "Too short password");

        $request = new Request([], ["password" => "Password is long enough"]);
        $request->headers->add(["user-token" => "Abcd1234"]);
        $this->assertEquals(200, $test->updatePassword($request, ["user" => "kjell@kejpa.com"])->getStatusCode(), "Too short password");
    }

    public function testCheckToken() {
        $test = new LoginController($this->userRepository, $this->loginHandler, $this->emailExistsQuery);

        $request = new Request();
        $request->headers->add(["user-token" => "Fel"]);
        $this->assertEquals(405, $test->checkToken($request)->getStatusCode());

        $request->headers->add(["user-token" => "Abcd1234"]);
        $this->assertEquals(200, $test->checkToken($request)->getStatusCode());
    }

    public function testRegister() {
        $test = new LoginController($this->userRepository, $this->loginHandler, $this->emailExistsQuery);

        $request = new Request([],
                ["email" => "kjell.hansenkejpa.com", "password" => "Password is long enough",
            "firstname" => "Kjell", "lastname" => "Hansen"]);
        $this->assertEquals(400, $test->register($request)->getStatusCode(), "Bad username (email)");

        $request = new Request([],
                ["email" => "kjell@kejpa.com", "password" => "Password is long enough",
            "firstname" => "Kjell", "lastname" => "Hansen"]);
        $this->assertEquals(400, $test->register($request)->getStatusCode(), "Email exists");

        $request = new Request([],
                ["email" => "kjell.hansen@kejpa.com", "password" => "pwd",
            "firstname" => "Kjell", "lastname" => "Hansen"]);
        $this->assertEquals(400, $test->register($request)->getStatusCode(), "Password too short");

        $request = new Request([],
                ["email" => "kjell.hansen@kejpa.com", "password" => "Password is long enough",
            "firstname" => "Kjell", "lastname" => "Hansen"]);
        $this->assertEquals(200, $test->register($request)->getStatusCode(), "Password too short");
    }

}
