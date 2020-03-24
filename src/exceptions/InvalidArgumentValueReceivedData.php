<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\exceptions;

use LogicException;
use Throwable;

/**
 * Class InvalidArgumentValueReceivedData
 * @package wtfproject\yii\argumentresolver\exceptions
 */
final class InvalidArgumentValueReceivedData extends LogicException
{
    /**
     * @var string
     */
    private $parameter;

    /**
     * InvalidParameterReceivedData constructor.
     * @param string $parameter
     * @param string $message
     * @param int $code
     * @param \Throwable $previous
     */
    public function __construct(string $parameter, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->parameter = $parameter;
    }

    /**
     * @return string
     */
    public function getParameter(): string
    {
        return $this->parameter;
    }
}
