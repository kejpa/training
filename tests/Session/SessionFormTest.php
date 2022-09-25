<?php

declare (strict_types=1);

namespace tests\Session;

use PHPUnit\Framework\TestCase;
use trainingAPI\Framework\ChainOfResponse\ChainOfResponseValidator;
use trainingAPI\Session\SessionForm;

/**
 * Description of SessionFromTest
 *
 * @author kjell
 */
final class SessionFormTest extends TestCase {

    private $validatorStub;

    public function setUp():void {
        $this->validatorStub = $this->getMockBuilder(ChainOfResponseValidator::class)
                ->disableOriginalConstructor()
                ->disableOriginalClone()
                ->disableArgumentCloning()
                ->disallowMockingUnknownTypes();
    }

    public function testValidationErrors() {
        $stub = $this->validatorStub->getMock();
        $stub->method('validate')->willReturn(true);
        $stub->method("getErrors")->willReturn(["Testing", "testing"]);

        $validators["date"] = $stub;
        $test = new SessionForm("","", "2022-02-02", "1000", "Testing testing", $validators);

        $this->assertEquals(false, $test->hasValidationErrors());
        $this->assertEquals(2, count($test->getValidationErrors()));
    }

    public function testNoValidationErrors() {
        $stub = $this->validatorStub->getMock();

        $stub->method('validate')->willReturn(false);
        $stub->method("getErrors")->willReturn([]);
        $validators["date"] = $stub;
        $test = new SessionForm("","", "2022-02-02", "1000", "Testing testing", $validators);
        $this->assertEquals(true, $test->hasValidationErrors());
        $this->assertEquals(0, count($test->getValidationErrors()));
    }

    public function testToCommand() {
        $validators = [];
        $test = new SessionForm("","", "2022-02-02", "1000", "Testing testing", $validators);
        $this->assertEquals(-1, $test->toCommand(1)->getId());

        $test = new SessionForm("","5", "2022-02-02", "1000", "Testing testing", $validators);
        $this->assertEquals(5, $test->toCommand(1)->getId());
    }

}
