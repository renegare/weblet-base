<?php

namespace Renegare\Weblet\Base;

use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Psr\Log\LoggerAwareInterface;

interface LoggerInterface extends LoggerAwareInterface, PsrLoggerInterface {}
