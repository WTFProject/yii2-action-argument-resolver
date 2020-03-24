<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\resolvers\value;

use ReflectionParameter;
use wtfproject\yii\argumentresolver\config\ArgumentValueResolverConfigurationInterface as Configuration;
use wtfproject\yii\argumentresolver\config\ComponentConfiguration;
use Yii;
use yii\base\Application;
use yii\base\InvalidConfigException;
use yii\di\Instance;

/**
 * Class ComponentArgumentValueResolver
 * @package wtfproject\yii\argumentresolver\resolvers\value
 */
class ComponentArgumentValueResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritDoc}
     */
    public function supports(
        ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null
    ): bool {
        return false === $parameter->isOptional()
            && null !== $parameter->getClass()
            && $configuration instanceof ComponentConfiguration;
    }

    /**
     * {@inheritDoc}
     *
     * @param \wtfproject\yii\argumentresolver\config\ComponentConfiguration $configuration
     *
     * @return object
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function resolve(ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null)
    {
        $module = $configuration->currentModule ?? Yii::$app;

        if (false === empty($configuration->module)) {
            $currentModule = $module;
            $module = $currentModule->getModule($configuration->module);

            if (null === $module) {
                throw new InvalidConfigException(\sprintf(
                    'Can not retrieve module "%s" from "%s".',
                    $configuration->module,
                    $currentModule instanceof Application ? 'application' : ($currentModule->getUniqueId() . ' module')
                ));
            }
        }

        return Instance::ensure($configuration->component, $parameter->getClass()->getName(), $module);
    }
}
