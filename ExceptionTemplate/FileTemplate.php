<?php

namespace Renegare\Weblet\Base\ExceptionTemplate;

use Symfony\Component\Debug\Exception\FlattenException;

class FileTemplate extends DefaultTemplate {

    protected $file;

    public function __construct($file) {
        $this->file = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(FlattenException $exception, $debug = false) {
        return file_exists($this->file)? file_get_contents($this->file) : parent::getContent($exception, $debug);
    }
}
