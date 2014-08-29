<?php

namespace Renegare\Weblet\Base\Test\Functional;

use Renegare\Weblet\Base\Weblet;
use Renegare\Weblet\Base\Test\WebletTestCase;
use Symfony\Component\HttpFoundation\Response;

class HealthCheckTest extends WebletTestCase {

    public function testHealthCheck() {
        $client = $this->createClient();
        $client->followRedirects(true);
        $client->request('GET', '/_healthcheck');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('All Good!', $response->getContent());
    }

    public function testHealthCheckWithTrailingSlash() {
        $client = $this->createClient();
        $client->request('GET', '/_healthcheck/');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('All Good!', $response->getContent());
    }

    public function testCustomHealthCheck() {
        $app = new Weblet(['healthcheck.uri' => '/_customhealthcheck/']);
        $client = $this->createClient([], $app);
        $client->request('GET', '/_customhealthcheck/');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('All Good!', $response->getContent());
    }
}
