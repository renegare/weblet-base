<?php

namespace Renegare\Weblet\Base\Test\Functional;

use Renegare\Weblet\Base\Weblet;
use Renegare\Weblet\Base\Test\WebletTestCase;
use Symfony\Component\HttpFoundation\Response;
use org\bovigo\vfs\vfsStream;

class LoggingTest extends WebletTestCase {

    protected $virtualFS;

    public function provideTestEnableLoggingData() {
        return [
            ['myapp', function() {
                $virtualFS = vfsStream::setup('tmp');
                return new Weblet('myapp', ['monolog.logfile' => $virtualFS->url() . '/weblet.log']);
            }],
            ['weblet', function() {
                $virtualFS = vfsStream::setup('tmp');
                return new Weblet(['monolog.logfile' => $virtualFS->url() . '/weblet.log']);
            }],
            ['anothername', function() {
                $virtualFS = vfsStream::setup('tmp');
                return new Weblet('differentname', [
                    'monolog.name' => 'anothername',
                    'monolog.logfile' => $virtualFS->url() . '/weblet.log'
                ]);
            }]
        ];
    }

    /**
     * @dataProvider provideTestEnableLoggingData
     */
    public function testEnableLogging($expectedAppName, \Closure $createApp = null) {
        $app = $createApp();
        $app->get('/', function() use ($app) {
            $app['logger']->debug('> Request In');
            return 'All Good!';
        });

        $app->enableLogging();

        $client = $this->createClient([], $app);
        $client->request('GET', '/');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('All Good!', $response->getContent());

        $logFilePath = $app['monolog.logfile'];
        $logContents = file_get_contents($logFilePath);
        $this->assertContains('> Request In', $logContents);
        $this->assertContains($expectedAppName . '.DEBUG', $logContents);
    }
}
