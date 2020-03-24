<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\exceptions;

use LogicException;
use Throwable;

/**
 * Class MissingArgumentsException
 * @package wtfproject\yii\argumentresolver\exceptions
 */
final class ArgumentsMissingException extends LogicException
{
    /**
     * @var array
     */
    private $missing;

    /**
     * ArgumentsMissingException constructor.
     * @param array $missing
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(array $missing, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->missing = $missing;
    }

    /**
     * @return array
     */
    public function getMissing(): array
    {
        return $this->missing;
    }
}
