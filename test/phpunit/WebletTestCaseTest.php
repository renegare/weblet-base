<?php

namespace Renegare\Weblet\Base\Test;

class WebletTestCaseTest extends WebletTestCase {

    public function testInstanceOfPHPUnit_Framework_Test() {
        $this->assertInstanceOf('PHPUnit_Framework_Test', $this);
    }

    public function testGetApplication() {
        $app = $this->getApplication();
        $this->assertInstanceOf('Renegare\Weblet\Base\Weblet', $app);
        $this->assertSame($this->app, $app);

        $this->app = null;
        $expectedNewAppInstance = $this->getApplication();
        $this->assertInstanceOf('Renegare\Weblet\Base\Weblet', $expectedNewAppInstance);
        $this->assertNotSame($app, $expectedNewAppInstance);
    }
}
