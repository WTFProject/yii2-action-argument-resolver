<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\config;

use yii\base\BaseObject;
use yii\base\InvalidConfigException;

/**
 * Class ComponentArgumentValueResolverConfiguration
 * @package wtfproject\yii\argumentresolver\config
 */
class ComponentArgumentValueResolverConfiguration extends BaseObject implements ArgumentValueResolverConfigurationInterface
{
    /**
     * @var string
     */
    public $module = '';

    /**
     * @var string
     */
    public $component;

    /**
     * ComponentArgumentValueResolverConfiguration constructor.
     * @param array $config
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        if (null !== $this->module && false === \is_string($this->module)) {
            throw new InvalidConfigException('Parameter "module" can be string or null.');
        }

        if (false === \is_string($this->component) || '' === $this->component) {
            throw new InvalidConfigException('Parameter "component" must be not empty string.');
        }
    }
}
