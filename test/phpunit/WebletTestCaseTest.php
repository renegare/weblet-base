<?php

namespace Renegare\Weblet\Base\Test;

use Renegare\Weblet\Base\WebletTestCase as WTC;

class WebletTestCaseTest extends WTC {

    public function testInstanceOfPHPUnit_Framework_Test() {
        $this->assertInstanceOf('PHPUnit_Framework_Test', $this);
    }

}
