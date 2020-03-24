<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\resolvers\value;

use ReflectionParameter;
use wtfproject\yii\argumentresolver\config\ArgumentValueResolverConfigurationInterface as Configuration;

/**
 * Interface ArgumentValueResolverInterface
 * @package wtfproject\yii\argumentresolver\resolvers\value
 */
interface ArgumentValueResolverInterface
{
    /**
     * @param \ReflectionParameter $parameter
     * @param array &$requestParams
     * @param \wtfproject\yii\argumentresolver\config\ArgumentValueResolverConfigurationInterface|null $configuration
     *
     * @return bool
     */
    public function supports(
        ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null
    ): bool;

    /**
     * @param \ReflectionParameter $parameter
     * @param array &$requestParams
     * @param \wtfproject\yii\argumentresolver\config\ArgumentValueResolverConfigurationInterface|null $configuration
     *
     * @return mixed
     */
    public function resolve(ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null);
}
