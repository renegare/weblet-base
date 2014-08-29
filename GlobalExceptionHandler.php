<?php

namespace Renegare\Weblet\Base;

use Symfony\Component\HttpKernel\Debug\ErrorHandler;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;

class GlobalExceptionHandler extends ExceptionHandler implements LoggerInterface {
    use LoggerTrait;

    private static $exceptionHandler;
    /** @var ExceptionTemplateInterface */
    protected $template;
    /** @var bool */
    protected $debug;

    /**
     * {@inheritdoc}
     */
    public function __construct($debug = true, $charset = 'UTF-8')
    {
        $this->debug = $debug;
        parent::__construct($debug);
    }

    /**
     * {@inheritdoc}
     */
    public static function register($debug = false) {
        if(!self::$exceptionHandler) {
            ErrorHandler::register();
            self::$exceptionHandler = parent::register($debug);
        }

        return self::$exceptionHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(FlattenException $exception)
    {
        return $this->getErrorTemplate()->getContent($exception, $this->debug);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $exception)
    {
        $this->critical('Uncaught application exception', ['exception' => $exception]);
        $response = $this->createResponse($exception);
        $response->sendHeaders();
        $response->sendContent();
    }

    /**
     * {@inheritdoc}
     */
    public function createResponse($exception)
    {
        if (!$exception instanceof FlattenException) {
            $exception = FlattenException::create($exception);
        }

        return new Response($this->getContent($exception), $exception->getStatusCode(), $exception->getHeaders());
    }

    /**
     * enable exceptions to be handled in debug mode (e.g display full stack trace)
     */
    public function enableDebugMode() {
        $this->debug = true;
    }

    /**
     * get the configured error template. DefaulTemplate is ... the defualt
     * @return ExceptionTemplateInterface
     */
    public function getErrorTemplate() {
        if(!$this->template) {
            $this->setErrorTemplate(new ExceptionTemplate\DefaultTemplate);
        }

        return $this->template;
    }

    /**
     * set the error template
     * @param string|ExceptionTemplateInterface $template - string arguments are converted into ExceptionTemplate\FileTemplate
     */
    public function setErrorTemplate($template) {
        if(is_string($template)) {
            $template = new ExceptionTemplate\FileTemplate($template);
        }

        if(!($template instanceOf ExceptionTemplateInterface)) {
            throw new \InvalidArgumentException('Error template must be a string or an instance of "Renegare\Weblet\Base\ExceptionTemplateInterface"');
        }

        $this->template = $template;
    }
}
