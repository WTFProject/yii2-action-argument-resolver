<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\resolvers\value;

use ReflectionParameter;
use wtfproject\yii\argumentresolver\config\ArgumentValueResolverConfigurationInterface as Configuration;

/**
 * Class DefaultValueResolver
 * @package wtfproject\yii\argumentresolver\resolvers\value
 */
class DefaultValueResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritDoc}
     */
    public function supports(
        ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null
    ): bool {
        return false === \array_key_exists($parameter->getName(), $requestParams)
            && $parameter->isDefaultValueAvailable();
    }

    /**
     * {@inheritDoc}
     *
     * @throws \ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null)
    {
        return $parameter->getDefaultValue();
    }
}
