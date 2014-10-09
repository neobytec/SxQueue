<?php

namespace SxQueue\Controller\Exception;

use SxQueue\Exception\ExceptionInterface;
use RuntimeException;

class WorkerProcessException extends RuntimeException implements ExceptionInterface
{
}
