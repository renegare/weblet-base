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
        $app = new Weblet(['debug' => true]);
        set_exception_handler(null);
        return $app;
    }

    /**
     * configure a given weblet
     * @param Weblet $app
     */
    public function configureApplication(Weblet $app) {
        $app['exception_handler']->disable();
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

    /**
     * given cookie is being used to save session data, unserialize and return session data
     * @param Client $client - containing the cookie
     * @param string $cookieName - name of session cookie
     * @return array|null if not found or incorrect format
     */
    public function getCookieSessionData(Client $client, $cookieName) {
        $cookieJar = $client->getCookieJar();
        $this->assertCount(1, $cookieJar->all());
        $cookie = $cookieJar->get($cookieName);
        return @unserialize(@unserialize($cookie->getValue())[1]);
    }

    /**
     * get full name of the test (including full class name and data set index)
     */
    public function getFullName() {
        return sprintf("\n%s::%s", get_class($this), $this->getName());
    }

    /**
     * get a service from the di/application
     * @param string $name
     * @return mixed
     */
    public function getService($name) {
        return $this->getApplication()[$name];
    }
}
