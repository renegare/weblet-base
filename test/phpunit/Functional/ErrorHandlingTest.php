<?php

namespace Renegare\Weblet\Base\Test\Functional;

use Renegare\Weblet\Base\Weblet;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\Process\PhpProcess;
use Symfony\Component\DomCrawler\Crawler;

class ErrorHandlingTest extends \PHPUnit_Framework_TestCase {

    public function testErrorsAreHandled() {
        $process = new PhpProcess(<<<'EOF'
<?php

    // test env Setup
    require_once 'vendor/autoload.php';

    if (function_exists('xdebug_disable')) {
        xdebug_disable();
    }

    use org\bovigo\vfs\vfsStream;
    $rootFS = vfsStream::setUp('tmp')->url();
    // test env Setup End

    $app = new Renegare\Weblet\Base\Weblet;
    $app->doSomethingFatal();
EOF
);

        $process->run();
        $html = $process->getOutput();
        $crawler = new Crawler($html);
        $bodyText = trim($crawler->filter('body')->text());
        $this->assertEquals('Whoops, looks like something went wrong.', $bodyText);
        $this->assertNotContains('doSomethingFatal', $bodyText);
        $this->assertNotContains('Renegare\Weblet\Base\Weblet', $bodyText);
    }

    public function testStackTraceIsDisplayedInDebugMode() {
        $process = new PhpProcess(<<<'EOF'
<?php

    // test env Setup
    require_once 'vendor/autoload.php';

    if (function_exists('xdebug_disable')) {
        xdebug_disable();
    }

    use org\bovigo\vfs\vfsStream;
    $rootFS = vfsStream::setUp('tmp')->url();
    // test env Setup End

    $app = new Renegare\Weblet\Base\Weblet(['debug' => true]);
    $app->doSomethingFatal();
EOF
);

        $process->run();
        $html = $process->getOutput();
        $crawler = new Crawler($html);
        $bodyText = trim($crawler->filter('body')->text());
        $this->assertContains('doSomethingFatal', $bodyText);
        $this->assertContains('Renegare\Weblet\Base\Weblet', $bodyText);
    }

    public function testConfiguredErrorFileIsUsed() {
        $process = new PhpProcess(<<<'EOF'
<?php

    // test env Setup
    require_once 'vendor/autoload.php';
    if (function_exists('xdebug_disable')) xdebug_disable();
    use org\bovigo\vfs\vfsStream;
    $rootFs = vfsStream::setUp('tmp')->url();
    file_put_contents($rootFs . '/error.html', '<h1>Custom Error Message ...</h1>');
    // test env Setup End

    $app = new Renegare\Weblet\Base\Weblet([
        'error.template' => $rootFs . '/error.html'
    ]);

    $app->doSomethingFatal();
EOF
);
        $process->run();
        $this->assertEquals('<h1>Custom Error Message ...</h1>', $process->getOutput());
    }

    public function testConfiguredErrorTemplateIsUsed() {
        $process = new PhpProcess(<<<'EOF'
<?php

    // test env Setup
    require_once 'vendor/autoload.php';
    if (function_exists('xdebug_disable')) xdebug_disable();

    class CustomErrorTemplate implements Renegare\Weblet\Base\ExceptionTemplateInterface {
        public function getContent(Symfony\Component\Debug\Exception\FlattenException $exception, $debug = false) {
            return 'Custom error message';
        }
    }
    // test env Setup End

    $app = new Renegare\Weblet\Base\Weblet([
        'error.template' => new CustomErrorTemplate
    ]);

    $app->doSomethingFatal();
EOF
);
        $process->run();
        $this->assertEquals('Custom error message', $process->getOutput());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidErrorTemplateConfig() {
        $app = new Weblet([
            'error.template' => new \stdClass
        ]);
    }
}
