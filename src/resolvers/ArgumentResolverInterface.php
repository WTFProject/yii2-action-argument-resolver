<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\resolvers;

use ReflectionMethod;

/**
 * Interface ArgumentResolverInterface
 * @package wtfproject\yii\argumentresolver\resolvers
 */
interface ArgumentResolverInterface
{
    /**
     * @param \ReflectionMethod $actionMethod
     * @param array &$requestParams
     * @param array &$configuration
     *
     * @return array
     */
    public function resolve(ReflectionMethod $actionMethod, array &$requestParams, array &$configuration = []): array;
}
