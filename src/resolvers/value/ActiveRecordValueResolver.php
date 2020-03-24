<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\resolvers\value;

use ReflectionParameter;
use wtfproject\yii\argumentresolver\config\ActiveRecordConfiguration;
use wtfproject\yii\argumentresolver\config\ArgumentValueResolverConfigurationInterface as Configuration;

/**
 * Class ActiveRecordValueResolver
 * @package wtfproject\yii\argumentresolver\resolvers\value
 */
class ActiveRecordValueResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritDoc}
     */
    public function supports(
        ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null
    ): bool {
        return null !== ($reflectionClass = $parameter->getClass())
            && $configuration instanceof ActiveRecordConfiguration;
    }

    /**
     * {@inheritDoc}
     *
     * @param \wtfproject\yii\argumentresolver\config\ActiveRecordConfiguration $configuration
     *
     * @return \yii\db\ActiveRecordInterface|null
     */
    public function resolve(
        ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null
    ) {
        if (\is_callable($configuration->findCallback)) {
            $model = \call_user_func($configuration->findCallback, $requestParams);
        }


        //TODO: if find callable present: call

        //TODO: if empty and parameter not null, throw default or setted notFoundHandler

        //TODO: if request param not exists and parameter nullable, return null

        //TODO: call find by method and attribute

        //TODO: if not null parameter, throw default or setted notFoundHandler

        //TODO: return null
    }

}
