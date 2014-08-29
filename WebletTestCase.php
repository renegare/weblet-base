<?php

namespace Renegare\Weblet\Base;

use Symfony\Component\HttpKernel\Client;

/**
 * Some methods were lifted from Silex\WebTestCase #credits
 */
class WebletTestCase extends \PHPUnit_Framework_TestCase {

    /** @var Weblet */
    protected $app;

    /**
     * creates an instance of weblet for each test
     */
    public function setUp() {
        $this->app = $this->createApplication();
    }

    /**
     * Create an instance of Weblet
     * @return Weblet
     */
    public function createApplication() {
        $app = new Weblet;
        return $app;
    }

    /**
     * Creates a Client.
     *
     * @param array $server An array of server parameters
     * @param Weblet $app an instance of weblet to use client with
     *
     * @return Client A Client instance
     */
    public function createClient(array $server = array(), Weblet $app = null) {
        return new Client($app? $app : $this->app, $server);
    }

    public function getCookieSessionData(Client $client, $cookieName) {
        $cookieJar = $client->getCookieJar();
        $this->assertCount(1, $cookieJar->all());
        $cookie = $cookieJar->get($cookieName);
        return unserialize(unserialize($cookie->getValue())[1]);
    }
}
