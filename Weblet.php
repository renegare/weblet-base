<?php

namespace Renegare\Weblet\Base;

use Silex\Application;

class Weblet extends Application {

    /**
     * Application constructor, initialises Error, Exception handlers andsets up config values.
     * With the exception of the first argument, all args thereafter can be an
     * array of config or string yaml file paths containing configuration.
     * Note:
     * - yaml files are not required to exist, no errors will be thrown
     * - order of config args affect what is overriden (similar to array_merge)
     * @param string $name - app name used internally and set under 'app.name' in Pimple
     * @param string|array ...$configs - variable args, that can be a yaml file path or a hash array of values
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
}
