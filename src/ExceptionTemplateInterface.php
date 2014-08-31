<?php

namespace Renegare\Weblet\Base;

use Symfony\Component\Debug\Exception\FlattenException;

interface ExceptionTemplateInterface {
    /**
     * Render the exception that is passed through
     * @param FlattenException $exception
     * @param bool $debug - environment mode
     * @return string
     */
    public function getContent(FlattenException $exception, $debug = false);
}
