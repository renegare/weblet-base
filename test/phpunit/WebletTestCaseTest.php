<?php

namespace Renegare\Weblet\Base\Test;

use Renegare\Weblet\Base\WebletTestCase;

class WebletTestCaseTest extends WebletTestCase {

    public function testInstanceOfPHPUnit_Framework_Test() {
        $this->assertInstanceOf('PHPUnit_Framework_Test', $this);
    }

}
