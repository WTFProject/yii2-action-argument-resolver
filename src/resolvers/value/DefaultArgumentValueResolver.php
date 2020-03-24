<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\resolvers\value;

use ReflectionParameter;
use wtfproject\yii\argumentresolver\config\ArgumentValueResolverConfigurationInterface as Configuration;

/**
 * Class DefaultArgumentValueResolver
 * @package wtfproject\yii\argumentresolver\resolvers\value
 */
class DefaultArgumentValueResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritDoc}
     */
    public function supports(
        ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null
    ): bool {
        return false === \array_key_exists($parameter->getName(), $requestParams)
            && false === $parameter->isVariadic()
            && (
                $parameter->isDefaultValueAvailable()
                || (null !== ($type = $parameter->getType()) && $type->allowsNull())
            );
    }

    /**
     * {@inheritDoc}
     *
     * @throws \ReflectionException
     */
    public function resolve(ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null)
    {
        return $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;
    }
}
