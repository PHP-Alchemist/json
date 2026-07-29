<?php

declare(strict_types=1);

namespace PHPAlchemist\Json\Exception;

final class BadJsonException extends \Exception
{
    protected $message = 'Invalid JSON';
}
