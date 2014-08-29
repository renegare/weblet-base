<?php

namespace Renegare\Weblet\Base;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Renegare\SilexCSH\CookieSessionServiceProvider;

class Weblet extends Application {

    /** @var boolean */
    protected $cookieSessionEnabled;

    /**
     * Application constructor, initialises Error, Exception handlers andsets up config values.
     * With the exception of the first argument, all args thereafter can be an
     * array of config or string yaml file paths containing configuration.
     * Note:
     * - yaml files are not required to exist, no errors will be thrown
     * - order of config args affect what is overriden (similar to array_merge)
     * @param string $name - app name used internally and set under 'app.name' in Pimple
     * @param string|array ...$configs - variable args, that can be a yaml file path or a hash array of values
     * - boolean 'debug' - controls the debug mode of the application
     * - string|ExceptionTemplateInterface 'error.template' - string is assumed to be a file path, else expects it to be an ExceptionTemplateInterface
     */
    public function __construct($name = 'weblet') {
        $exceptionHandler = GlobalExceptionHandler::register();
        $config = func_get_args();
        array_shift($config);
        $values = call_user_func_array(['Renegare\Constants\Constants', 'compile'], $config);

        if(isset($values['debug']) && (!!$values['debug']) === true) {
            $exceptionHandler->enableDebugMode();
        }

        parent::__construct($values);
        $this['app.name'] = $name;

        if(isset($this['error.template'])) {
            $exceptionHandler->setErrorTemplate($this['error.template']);
        }
    }

    /**
     * enables the weblet to store session data in a cookie (please session data to a minimum!)
     */
    public function enableCookieSession() {
        if(!$this->cookieSessionEnabled) {
            $this->cookieSessionEnabled = true;
            $this->doRegister(new CookieSessionServiceProvider, ['session.cookie.options']);
        }
    }

    /**
     * Silex Service Providers come with their default config. Using built in weblet functionality
     * this method prevents preconfigured values from being overriden.
     * @param ServiceProviderInterface $provider
     * @param array $configKey = []
     */
    protected function doRegister(ServiceProviderInterface $provider, array $configKeys = []) {
        $config = [];
        foreach($configKeys as $key) {
            if(isset($this[$key])) {
                $config[$key] = $this[$key];
            }
        }
        $this->register($provider, $config);
    }
}
