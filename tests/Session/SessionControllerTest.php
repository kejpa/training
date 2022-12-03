<?php

declare (strict_types=1);

namespace tests\Session;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use tests\JsonDB\JsonEmailExistsQuery;
use tests\JsonDB\JsonSessionIdExists;
use tests\JsonDB\JsonSessionRepository;
use tests\JsonDB\JsonUserRepository;
use trainingAPI\Framework\ChainOfResponse\Validator\IdExistsValidator;
use trainingAPI\Login\LoginController;
use trainingAPI\Login\LoginHandler;
use trainingAPI\Session\SessionController;

/**
 * Description of SessionControllerTest
 *
 * @author kjell
 */
final class SessionControllerTest extends TestCase {

    private $sessionRepository;
    private $loginController;
    private $loginRepository;
    private $idExistsValidator;

    public function __construct() {
        parent::__construct();

        $this->loginRepository = new JsonUserRepository();
        $this->sessionRepository = new JsonSessionRepository();
        $loginHandler=new LoginHandler($this->loginRepository);
        $emailExists=new JsonEmailExistsQuery();
        $this->loginController = new LoginController($this->loginRepository, $loginHandler,$emailExists);
        $jsonSessionIdExists = new JsonSessionIdExists();
        $this->idExistsValidator = new IdExistsValidator($jsonSessionIdExists);
    }

    public function testGetAllSessions() {
        $test = new SessionController($this->loginController, $this->sessionRepository, $this->idExistsValidator);
        $request = new Request();
        $request->headers->add(["user-token" => "Abcd1234"]);
        $response = $test->getAllSessions($request);
        $json = json_decode($response->getContent());
        $this->assertEquals(6, count($json->sessions));
    }

    public function testGetSession() {
        $test = new SessionController($this->loginController, $this->sessionRepository, $this->idExistsValidator);
        $request = new Request();
        $request->headers->add(["user-token" => "Abcd1234"]);
        $response = $test->getSession($request, ["id" => 1]);
        $json = json_decode($response->getContent());

        $this->assertEquals(1, $json->sessions->userid);
    }

    public function testAddSession() {
        $test = new SessionController($this->loginController, $this->sessionRepository, $this->idExistsValidator);
        $request = new Request();
        $request->initialize($request->query->all(), $request->request->all(),
                $request->attributes->all(), [], [], $request->server->all(),
            '{"date":"' . date("Y-m-d", strtotime("yesterday")) . '",' .
            '"length":1000,"description": "Liten text"}');
        
        $request->headers->add(["user-token" => "Abcd1234"]);
        $request->setMethod("POST");

        $response = $test->addSession($request);
        $json = json_decode($response->getContent());
        $this->assertEquals(8, $json->session->id);
    }

    public function testUpdateSession() {
        $test = new SessionController($this->loginController, $this->sessionRepository, $this->idExistsValidator);
        $request = new Request();
        $request->initialize($request->query->all(), $request->request->all(),
                $request->attributes->all(), [], [], $request->server->all(),
                '{"id":1,"date":"' . date("Y-m-d", strtotime("yesterday")) . '"'
                . ',"length":1000, "description":"Liten text"}'
        );
        $request->headers->add(["user-token" => "Abcd1234"]);
        $request->setMethod("PUT");
        $response = $test->updateSession($request, ["id" => 1]);
        $json = json_decode($response->getContent());

        $this->assertEquals(1, $json->rowsAffected);
    }

    public function testInvalidUser() {
        $test = new SessionController($this->loginController, $this->sessionRepository, $this->idExistsValidator);
        $request = new Request();
        $request->headers->add(["user-token" => "Fel"]);
        $response = $test->getAllSessions($request);
        $json = json_decode($response->getContent());
        $this->assertEquals("Validation failed", $json->message[0]);

        $response = $test->getSession($request, ["id" => "1"]);
        $json = json_decode($response->getContent());
        $this->assertEquals("Validation failed", $json->message[0]);

        $response = $test->addSession($request);
        $json = json_decode($response->getContent());
        $this->assertEquals("Validation failed", $json->message[0]);

        $response = $test->updateSession($request, ["id" => "1"]);
        $json = json_decode($response->getContent());
        $this->assertEquals("Validation failed", $json->message[0]);

        $response = $test->deleteSession($request, ["id" => "1"]);
        $json = json_decode($response->getContent());
        $this->assertEquals("Validation failed", $json->message[0]);
    }

    public function testMissingOrInvalidKey() {
        $test = new SessionController($this->loginController, $this->sessionRepository, $this->idExistsValidator);
        $request = new Request();
        $request->headers->add(["user-token" => "Abcd1234"]);

        $response = $test->getSession($request, []);
        $json = json_decode($response->getContent());
        $this->assertEquals("Invalid id supplied", $json->message[1]);

        $response = $test->getSession($request, ["id" => "Fel"]);
        $json = json_decode($response->getContent());
        $this->assertEquals("Invalid id supplied", $json->message[1]);

        $response = $test->getSession($request, ["id" => "200"]);
        $json = json_decode($response->getContent());
        $this->assertNull($json->sessions);
    }

    public function testAddSessionValidationErrors() {
        $test = new SessionController($this->loginController, $this->sessionRepository, $this->idExistsValidator);
// Bad date
        $request = new Request();
        $request->initialize($request->query->all(), $request->request->all(),
                $request->attributes->all(), [], [], $request->server->all(),
                '{"date":"Bad date","length":1000, "description":"Liten text"}'
        );
        $request->headers->add(["user-token" => "Abcd1234"]);
        $request->setMethod("PUT");

        $response = $test->addSession($request);
        $this->assertEquals(400, $response->getStatusCode());
// Date in future
        $request = new Request();
        $request->initialize($request->query->all(), $request->request->all(),
                $request->attributes->all(), [], [], $request->server->all(),
                '{"date":"' . date("Y-m-d", strtotime("tomorrow")) . '","length":1000, "description":"Liten text"}'
        );
        $request->headers->add(["user-token" => "Abcd1234"]);
        $response = $test->addSession($request);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testUpdateSessionValidationErrors() {
        $test = new SessionController($this->loginController, $this->sessionRepository, $this->idExistsValidator);
// Bad date
        $request = new Request( ["id" => 1]);
        $request->initialize($request->query->all(), $request->request->all(),
                $request->attributes->all(), [], [], $request->server->all(),
                '{"id":1,"date":"Bad date","length":1000, "description":"Liten text"}'
        );
        $request->headers->add(["user-token" => "Abcd1234"]);
        $request->setMethod("PUT");
        $response = $test->updateSession($request, ["id" => 1]);
        $this->assertEquals(400, $response->getStatusCode());

// Date in future
        $request = new Request( ["id" => 1]);
        $request->initialize($request->query->all(), $request->request->all(),
                $request->attributes->all(), [], [], $request->server->all(),
                '{"id":1,"date":"' . date("Y-m-d", strtotime("tomorrow")) . '","length":1000, "description":"Liten text"}'
        );
        $request->headers->add(["user-token" => "Abcd1234"]);
        $request->setMethod("PUT");
        $response = $test->updateSession($request, ["id" => 1]);
        $this->assertEquals(400, $response->getStatusCode());

// body id and query id not matching
        $request = new Request( ["id" => 1]);
        $request->initialize($request->query->all(), $request->request->all(),
                $request->attributes->all(), [], [], $request->server->all(),
                '{"id":1,"date":"' . date("Y-m-d", strtotime("yesterday")) . '","length":1000, "description":"Liten text"}'
        );
        $request->headers->add(["user-token" => "Abcd1234"]);
        $request->setMethod("PUT");
        $response = $test->updateSession($request, ["id" => 2]);
        $this->assertEquals(400, $response->getStatusCode());

// id not matching user
        $request = new Request(["id" => 2]);
        $request->initialize($request->query->all(), $request->request->all(),
                $request->attributes->all(), [], [], $request->server->all(),
                '{"id":2,"date":"' . date("Y-m-d", strtotime("yesterday")) . '","length":1000, "description":"Liten text"}'
        );
        $request->headers->add(["user-token" => "Abcd1234"]);
        $request->setMethod("PUT");
        $response = $test->updateSession($request, ["id" => 2]);
        $this->assertEquals(400, $response->getStatusCode());
    }
    public function testDeleteSession() {
        $test = new SessionController($this->loginController, $this->sessionRepository, $this->idExistsValidator);
        $request = new Request(["id" => 1]);
        $request->headers->add(["user-token" => "Abcd1234"]);
        $request->setMethod("DELETE");
        $response = $test->deleteSession($request, ["id" => 1]);
        $json = json_decode($response->getContent());

        $this->assertEquals(1, $json->rowsAffected);
    }
    public function testDeleteSessionValidationErrors() {
        $test = new SessionController($this->loginController, $this->sessionRepository, $this->idExistsValidator);
        $request = new Request(["id" => 2]);
        $request->headers->add(["user-token" => "Abcd1234"]);
        $request->setMethod("DELETE");
        $response = $test->deleteSession($request, ["id" => 2]);
        $json = json_decode($response->getContent());

        $this->assertEquals(0, $json->rowsAffected);
        
    }
}
