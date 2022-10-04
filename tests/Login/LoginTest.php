<?php
declare (strict_types=1);

namespace tests\Login;

use PHPUnit\Framework\TestCase;
use trainingAPI\Login\Login;


/**
 * Description of LoginTest
 *
 * @author kjell
 */
final class LoginTest extends TestCase {

    public function testProperties() {
        $test = new Login("user", "password");
        $this->assertEquals("user", $test->getUsername());
        $this->assertEquals("password", $test->getPassword());
    }

}
