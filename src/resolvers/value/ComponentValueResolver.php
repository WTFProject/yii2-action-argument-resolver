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
 * Class ComponentValueResolver
 * @package wtfproject\yii\argumentresolver\resolvers\value
 */
class ComponentValueResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritDoc}
     */
    public function supports(
        ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null
    ): bool {
        return $configuration instanceof ComponentConfiguration && null !== $parameter->getClass();
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
                    'Can not retrieve module "%s" from %s.',
                    $configuration->module,
                    $currentModule instanceof Application ? '"application"' : ('"' . $currentModule->getUniqueId() . '" module')
                ));
            }
        }

        if (false === $module->has($configuration->component) && $parameter->allowsNull()) {
            return null;
        }

        if (false === $module->has($configuration->component)) {
            throw new InvalidConfigException(\sprintf(
                'Can not retrieve component "%s" from %s.',
                $configuration->component,
                $module instanceof Application ? '"application"' : ('"' . $module->getUniqueId() . '" module')
            ));
        }

        return Instance::ensure($configuration->component, $parameter->getClass()->getName(), $module);
    }
}
