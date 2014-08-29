<?php

namespace Renegare\Weblet\Base;

use Symfony\Component\Debug\Exception\FlattenException;

interface ExceptionTemplateInterface {
    public function getContent(FlattenException $exception, $debug = false);
}
