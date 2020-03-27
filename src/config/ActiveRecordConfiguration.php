<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\config;

use yii\base\BaseObject;
use yii\base\InvalidConfigException;

/**
 * Class ActiveRecordConfiguration
 * @package wtfproject\yii\argumentresolver\config
 */
class ActiveRecordConfiguration extends BaseObject implements ArgumentValueResolverConfigurationInterface
{
    /**
     * @var string
     */
    public $attribute;

    /**
     * @var callable|null
     */
    public $findCallback;

    /**
     * @var callable|null
     */
    public $notFoundCallback;

    /**
     * ActiveRecordConfiguration constructor.
     * @param array $config
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        if (null !== $this->attribute && (false === \is_string($this->attribute) || '' === $this->attribute)) {
            throw new InvalidConfigException('Parameter "attribute" must be not empty string or null.');
        }

        if (null !== $this->findCallback && false === \is_callable($this->findCallback)) {
            throw new InvalidConfigException('Parameter "findCallback" must be a callable.');
        }

        if (null !== $this->notFoundCallback && false === \is_callable($this->notFoundCallback)) {
            throw new InvalidConfigException('Parameter "notFoundCallback" must be a callable.');
        }
    }
}
