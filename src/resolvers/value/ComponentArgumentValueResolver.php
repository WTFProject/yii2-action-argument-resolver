<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\resolvers\value;

use ReflectionParameter;
use wtfproject\yii\argumentresolver\config\ArgumentValueResolverConfigurationInterface as Configuration;
use wtfproject\yii\argumentresolver\config\ComponentArgumentValueResolverConfiguration;
use Yii;

/**
 * Class ComponentArgumentValueResolver
 * @package wtfproject\yii\argumentresolver\resolvers\value
 */
class ComponentArgumentValueResolver implements ArgumentValueResolverInterface
{
    /**
     *{ @inheritDoc}
     */
    public function supports(
        ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null
    ): bool {
        return false === $parameter->isOptional()
            && null !== $parameter->getClass()
            && $configuration instanceof ComponentArgumentValueResolverConfiguration;
    }

    /**
     * {@inheritDoc}
     *
     * @param \wtfproject\yii\argumentresolver\config\ComponentArgumentValueResolverConfiguration $configuration
     *
     * @return object
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function resolve(ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null)
    {
        $module = Yii::$app;

        if (false === empty($configuration->module)) {
            $moduleIds = \explode('.', $configuration->module);

            foreach ($moduleIds as $moduleId) {
                $module = $module->getModule($moduleId);
            }
        }

        $component = $module->get($configuration->component);

        if (false === $parameter->getClass()->isInstance($component)) {
            throw new \RuntimeException();
            //TODO: throw exception
        }

        return $component;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigurationClass()
    {
        return ComponentArgumentValueResolverConfiguration::class;
    }
}
