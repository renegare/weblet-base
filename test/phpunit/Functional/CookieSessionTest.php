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
            ['WSC', function() {
                return new Weblet('name', [
                    'session.cookie.options' => ['name' => 'WSC']
                ]);
            }]
        ];
    }

    /**
     * @dataProvider provideTestEnableCookieSessionsData
     */
    public function testEnableCookieSessions($expectedCookieName, \Closure $configureCallback) {
        $app = $configureCallback();

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
        $data = $this->getCookieSessionData($client, $expectedCookieName);
        $this->assertEquals(['param' => 'value'], $data);
    }
}
