<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\config;

use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\base\Module;

/**
 * Class ComponentConfiguration
 * @package wtfproject\yii\argumentresolver\config
 */
class ComponentConfiguration extends BaseObject implements ArgumentValueResolverConfigurationInterface
{
    /**
     * @var \yii\base\Module|null
     */
    public $currentModule;

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

        if (null !== $this->currentModule && false === $this->currentModule instanceof Module) {
            throw new InvalidConfigException(
                'Parameter "currentModule" must be an instance of "\yii\base\Module".'
            );
        }

        if (null !== $this->module && false === \is_string($this->module)) {
            throw new InvalidConfigException('Parameter "module" can be string or null.');
        }

        if (false === \is_string($this->component) || '' === $this->component) {
            throw new InvalidConfigException('Parameter "component" must be not empty string.');
        }
    }
}
