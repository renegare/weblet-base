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
     * creates an instance of weblet and configure it for each test
     */
    public function setUp() {
        $app = $this->getApplication();
        $this->configureApplication($app);
    }

    /**
     * Create an instance of Weblet
     * @return Weblet
     */
    public function createApplication() {
        return new Weblet(['debug' => true]);
    }

    /**
     * configure a given weblet
     * @param Weblet $app
     */
    public function configureApplication(Weblet $app) {
        $app['exception_handler']->disable();
        set_exception_handler(null);
        $app['session.test'] = true;
    }

    /**
     * get existing app instance or create a new one if none does not exist
     * @return Weblet
     */
    public function getApplication() {
        if(!$this->app) {
            $this->app = $this->createApplication();
        }

        return $this->app;
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

    public function getFullName() {
        return sprintf("\n%s::%s", get_class($this), $this->getName());
    }
}
