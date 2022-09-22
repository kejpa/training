<?php

declare (strict_types=1);

namespace tests\Session;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use tests\JsonDB\JsonLoginRepository;
use tests\JsonDB\JsonSessionIdExists;
use tests\JsonDB\JsonSessionRepository;
use trainingAPI\Framework\ChainOfResponse\Validator\IdExistsValidator;
use trainingAPI\Login\LoginController;
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

        $this->loginRepository = new JsonLoginRepository();
        $this->sessionRepository = new JsonSessionRepository();
        $this->loginController = new LoginController($this->loginRepository);
        $jsonSessionIdExists = new JsonSessionIdExists();
        $this->idExistsValidator = new IdExistsValidator($jsonSessionIdExists);
    }

    public function testGetAllSessions() {
        $test = new SessionController($this->loginController, $this->sessionRepository, $this->idExistsValidator);
        $request = new Request(["user-token" => "Abcd1234"]);
        $response = $test->getAllSessions($request);
        $json = json_decode($response->getContent());
        $this->assertEquals(6, count($json->sessions));
    }

    public function testGetSession() {
        $test = new SessionController($this->loginController, $this->sessionRepository, $this->idExistsValidator);
        $request = new Request(["user-token" => "Abcd1234"]);
        $response = $test->getSession($request, ["id" => 1]);
        $json = json_decode($response->getContent());

        $this->assertEquals(1, $json->sessions->userid);
    }

    public function testAddSession() {
        $test = new SessionController($this->loginController, $this->sessionRepository, $this->idExistsValidator);
        $request = new Request(["user-token" => "Abcd1234",
            "date" => date("Y-m-d", strtotime("yesterday")),
            "length" => 1000,
            "description" => "Liten text"
        ]);
        $response = $test->addSession($request);
        $json = json_decode($response->getContent());

        $this->assertEquals(8, $json->session->id);
    }

    public function testUpdateSession() {
        $test = new SessionController($this->loginController, $this->sessionRepository, $this->idExistsValidator);
        $request = new Request(["user-token" => "Abcd1234"]);
        $request->initialize($request->query->all(), $request->request->all(),
                $request->attributes->all(), [], [], $request->server->all(),
                '{"id":1,"date":"' . date("Y-m-d", strtotime("yesterday")) . '"'
                . ',"length":1000, "description":"Liten text"}'
        );
        $request->setMethod("PUT");
        $response = $test->updateSession($request, ["id" => 1]);
        $json = json_decode($response->getContent());

        $this->assertEquals(1, $json->rowsAffected);
    }

}
