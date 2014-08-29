<?php

namespace Renegare\Weblet\Base\Test\Functional;

use Renegare\Weblet\Base\Weblet;
use Renegare\Weblet\Base\Test\WebletTestCase;
use Symfony\Component\HttpFoundation\Response;

class CookieSessionTest extends WebletTestCase {

    public function provideTestEnableCookieSessionsData() {
        return [
            ['PHPSESSIONID', function() {
                return new Weblet;
            }],
            ['WEBLET_SESSION', function() {
                return new Weblet([
                    'session.cookie.name' => 'WSC'
                ]);
            }]
        ];
    }

    /**
     * @dataProvider provideTestEnableCookieSessionsData
     */
    public function testEnableCookieSessions($expectedCookieName, \Closure $configureCallback) {
        $app = $configureCallback();
        set_exception_handler(null);
        $app->get('/', function() use ($app) {
            $app['session']->set('param', 'value');
            return 'All Good!';
        });

        $app->enableCookieSession();

        $client = $this->createClient([], $app);
        $client->request('GET', '/');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('All Good!', $response->getContent());
        return;
        $data = $this->getSessionData($client);
        $this->assertEquals(['param' => 'value'], $data);
    }
}
