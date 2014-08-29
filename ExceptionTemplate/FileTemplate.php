<?php

namespace Renegare\Weblet\Base\ExceptionTemplate;

use Symfony\Component\Debug\Exception\FlattenException;

/**
 * Tries to load the file and returns the content. Failing that it will fallback to the DefaultTemplate
 */
class FileTemplate extends DefaultTemplate {

    /** @var string */
    protected $file;

    /**
     * @param string $file
     */
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
