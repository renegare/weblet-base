<?php

namespace Renegare\Weblet\Base\Test\Functional;

use Renegare\Weblet\Base\Weblet;
use Renegare\Weblet\Base\Test\WebletTestCase;
use Symfony\Component\HttpFoundation\Response;

class CookieSessionTest extends WebletTestCase {

    public function provideTestEnableCookieSessionsData() {
        return [
            ['PHPSESSIONID', null],
            ['WSC', ['name' => 'WSC']]
        ];
    }

    /**
     * @dataProvider provideTestEnableCookieSessionsData
     */
    public function testEnableCookieSessions($expectedCookieName, $sessionConfig = null) {
        $app = $this->getApplication();

        if($sessionConfig) {
            $app['session.cookie.options'] = $sessionConfig;
        }

        $app->enableCookieSession();

        $app->get('/', function() use ($app) {
            $app['session']->set('param', 'value');
            return 'All Good!';
        });

        $client = $this->createClient([], $app);
        $client->request('GET', '/');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('All Good!', $response->getContent());
        $data = $this->getCookieSessionData($client, $expectedCookieName);
        $this->assertEquals(['param' => 'value'], $data);
    }
}
