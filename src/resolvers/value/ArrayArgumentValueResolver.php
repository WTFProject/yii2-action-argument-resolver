<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\resolvers\value;

use ReflectionParameter;
use wtfproject\yii\argumentresolver\config\ArgumentValueResolverConfigurationInterface as Configuration;

/**
 * Class ArrayArgumentValueResolver
 * @package wtfproject\yii\argumentresolver\resolvers\value
 */
class ArrayArgumentValueResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritDoc}
     */
    public function supports(
        ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null
    ): bool {
        return \array_key_exists($parameter->getName(), $requestParams)
            && $parameter->isArray()
            && false === $parameter->isVariadic();
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(
        ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null
    ): array {
        return (array)$requestParams[$parameter->getName()];
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigurationClass()
    {
    }
}
