<?php

namespace Renegare\Weblet\Base;

use Symfony\Component\HttpKernel\Debug\ErrorHandler;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;

class GlobalExceptionHandler extends ExceptionHandler{

    private static $exceptionHandler;

    protected $template;
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

    public function getErrorTemplate() {
        if(!$this->template) {
            $this->setErrorTemplate(new ExceptionTemplate\DefaultTemplate);
        }

        return $this->template;
    }

    public function setErrorTemplate($template) {
        if(is_string($template)) {
            $template = new ExceptionTemplate\FileTemplate($template);
        }

        if(!($template instanceOf ExceptionTemplateInterface)) {
            throw new \InvalidArgumentException('Error template must be a string or an instance of "Renegare\Weblet\Base\ExceptionTemplateInterface"');
        }

        $this->template = $template;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $exception)
    {
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

    public function enableDebugMode() {
        $this->debug = true;
    }
}
