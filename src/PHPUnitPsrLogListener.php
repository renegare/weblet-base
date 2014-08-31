<?php

namespace Renegare\Weblet\Base;

class PHPUnitPsrLogListener extends \PHPUnit_Framework_BaseTestListener {

    protected $failedTest = [];
    protected $logEntries = [];

    /**
     * {@inheritdoc}
     */
    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time) {
        if($test instanceof WebletTestCase) {
            $this->failedTest[] = $test->getFullName();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time) {
        if($test instanceof WebletTestCase) {
            $this->failedTest[] = $test->getFullName();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function startTest(\PHPUnit_Framework_Test $test) {
        if($test instanceof WebletTestCase) {
            $app = $test->getApplication();
            $testName = $test->getFullName();
            // echo "\n";
            // $this->writeln(md5(spl_object_hash($app)) . ' Listener.');

            $mockLogger = $test->getMock('Psr\Log\LoggerInterface');
            $mockLogger->expects($test->any())->method('log')->will($test->returnCallback(function($level, $message, $context) use ($testName) {
                $this->addMockLogEntry($testName, [
                    'level' => $level,
                    'message' => $message,
                    'context' => $context
                ]);
            }));

            $logLevels = ['debug', 'error', 'info', 'critical', 'notice', 'warning', 'alert', 'emergency'];
            foreach($logLevels as $method) {
                $mockLogger->expects($test->any())->method($method)->will($test->returnCallback(function($message, $context) use ($method, $mockLogger) {
                    $mockLogger->log($method, $message, $context);
                }));
            }

            $app['logger'] = $mockLogger;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function endTest(\PHPUnit_Framework_Test $test, $time) {
        if($test instanceof WebletTestCase && !in_array($test->getFullName(), $this->failedTest)) {
            unset($this->logEntries[$test->getFullName()]);
        }
    }

    public function addMockLogEntry($testName, $logEntry) {
        if(!isset($this->logEntries[$testName])) {
            $this->logEntries[$testName] = [];
        }

        $this->logEntries[$testName][] = $logEntry;
    }

    public function __destruct() {
        if(!count($this->logEntries)) return;

        $this->writeln('', 2);
        $this->writeln("======================================");
        $this->writeln("Mock Log of failed tests ...");
        $this->writeln("======================================", 2);

        foreach($this->logEntries as $testName => $entries) {
            $this->writeln(">>> START: $testName", 2);

            foreach($entries as $log) {
                $this->writeln(sprintf('[%s] %s %s', $log['level'], $log['message'], json_encode($this->normalize($log['context']))));
            }
            $this->writeln("<<< END: $testName", 3);
        }

        $this->writeln('');

        $this->failedTest = [];

    }

    protected function writeln($string = '', $newLineCount = 1) {
        echo $string . str_repeat("\n", $newLineCount);
    }

    /**
     * credits to Monolog Package (c) Jordi Boggiano <j.boggiano@seld.be>
     */
    protected function normalize($data)
    {
        if (null === $data || is_scalar($data)) {
            return $data;
        }

        if (is_array($data) || $data instanceof \Traversable) {
            $normalized = array();

            $count = 1;
            foreach ($data as $key => $value) {
                if ($count++ >= 1000) {
                    $normalized['...'] = 'Over 1000 items, aborting normalization';
                    break;
                }
                $normalized[$key] = $this->normalize($value);
            }

            return $normalized;
        }

        if ($data instanceof \DateTime) {
            return $data->format($this->dateFormat);
        }

        if (is_object($data)) {
            if ($data instanceof Exception) {
                return $this->normalizeException($data);
            }

            return sprintf("[object] (%s: %s)", get_class($data), $this->toJson($data, true));
        }

        if (is_resource($data)) {
            return '[resource]';
        }

        return '[unknown('.gettype($data).')]';
    }

    protected function normalizeException(Exception $e)
    {
        $data = array(
            'class' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile().':'.$e->getLine(),
        );

        $trace = $e->getTrace();
        foreach ($trace as $frame) {
            if (isset($frame['file'])) {
                $data['trace'][] = $frame['file'].':'.$frame['line'];
            } else {
                $data['trace'][] = json_encode($frame);
            }
        }

        if ($previous = $e->getPrevious()) {
            $data['previous'] = $this->normalizeException($previous);
        }

        return $data;
    }

    protected function toJson($data, $ignoreErrors = false)
    {
        // suppress json_encode errors since it's twitchy with some inputs
        if ($ignoreErrors) {
            if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
                return @json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

            return @json_encode($data);
        }

        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return json_encode($data);
    }

}
