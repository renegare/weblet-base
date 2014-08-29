<?php

namespace Renegare\Weblet\Base\Test\Functional;

use Renegare\Weblet\Base\Weblet;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\Yaml\Yaml;

class ConfigurationTest extends \PHPUnit_Framework_TestCase {

    public function testSetConstants() {
        $rootFs = vfsStream::setup('home');

        $configAFile = vfsStream::newFile('config_a.yml')->setContent(Yaml::dump([
            'PARAM_1' => 'value_1',
            'PARAM_3' => 'value_1'
        ]))->at($rootFs);

        $configBFile = vfsStream::newFile('config_b.yml')->setContent(Yaml::dump([
            'PARAM_2' => 'value_2',
            'PARAM_3' => 'value_2'
        ]))->at($rootFs);

        $app = new Weblet('Base Weblet', ['Test' => 'hmmm'], $configAFile->url(), $configBFile->url());

        $this->assertEquals('Base Weblet', $app['app.name']);
        $this->assertEquals('hmmm', $app['Test']);
        $this->assertEquals('value_1', $app['PARAM_1']);
        $this->assertEquals('value_2', $app['PARAM_2']);
        $this->assertEquals('value_2', $app['PARAM_3']);
    }
}
