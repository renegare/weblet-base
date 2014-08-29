<?php

namespace Renegare\Weblet\Base;

use Silex\Application;

class Weblet extends Application {

    /**
     * Application constructor. With the exception of the first argument, all
     * args thereafter can be an array of config or string yaml file paths
     * containing configuration. Please note:
     * - yaml files are not required to exist, no errors will be thrown
     * - 'app.root' config must be present somewhere (or an exception will be thrown for this!)
     * - order of config args affect what is overriden (similar to array_merge)
     * @param string $name - app name used internally and set under 'app.name' in Pimple
     * @param string|array ...$configs - variable args, that can be a yaml file path or a hash array of values
     * @throws \BadMethodCallException if 'app.root' is not set or its value does not exist on file system
     */
    public function __construct($name = 'weblet') {
        $config = func_get_args();
        array_shift($config);
        $values = call_user_func_array(['Renegare\Constants\Constants', 'compile'], $config);
        $values['app.name'] = $name;
        parent::__construct($values);

        if(!isset($this['app.root'])) {
            throw new \BadMethodCallException("'app.root' has not been set in your configuration");
        }

        if(!file_exists($this['app.root'])) {
            throw new \BadMethodCallException(sprintf("'app.root' does not exist on the fs: '%s'", $this['app.root']));
        }
    }
}
