<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\resolvers\value;

use ReflectionParameter;
use wtfproject\yii\argumentresolver\config\ArgumentValueResolverConfigurationInterface as Configuration;
use Yii;
use yii\web\Request;

/**
 * Class RequestValueResolver
 * @package wtfproject\yii\argumentresolver\resolvers\value
 */
class RequestValueResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function supports(
        ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null
    ): bool {
        return null !== ($reflectionClass = $parameter->getClass())
            && (
                $reflectionClass->getName() === Request::class || \is_subclass_of($reflectionClass->getName(), Request::class)
            )
            && (null !== Yii::$app->get('request', false) || $parameter->allowsNull());
    }

    /**
     * {@inheritDoc}
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function resolve(ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null)
    {
        return Yii::$app->get('request', false === $parameter->allowsNull());
    }
}
