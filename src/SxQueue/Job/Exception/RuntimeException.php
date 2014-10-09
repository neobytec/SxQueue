<?php

namespace SxQueue\Job\Exception;

use RuntimeException as BaseRuntimeException;
use SxQueue\Exception\ExceptionInterface;

/**
 * RuntimeException
 */
class RuntimeException extends BaseRuntimeException implements ExceptionInterface
{
}
