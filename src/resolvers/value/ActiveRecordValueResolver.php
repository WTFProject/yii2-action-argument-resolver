<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\resolvers\value;

use ReflectionParameter;
use wtfproject\yii\argumentresolver\config\ActiveRecordConfiguration;
use wtfproject\yii\argumentresolver\config\ArgumentValueResolverConfigurationInterface as Configuration;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\db\ActiveRecordInterface;
use yii\web\NotFoundHttpException;

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
        return $configuration instanceof ActiveRecordConfiguration
            && null !== ($reflectionClass = $parameter->getClass())
            && \is_subclass_of($reflectionClass->getName(), ActiveRecordInterface::class);
    }

    /**
     * {@inheritDoc}
     *
     * @param \wtfproject\yii\argumentresolver\config\ActiveRecordConfiguration $configuration
     *
     * @return \yii\db\ActiveRecordInterface|null
     *
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\NotSupportedException
     */
    public function resolve(
        ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null
    ) {
        $attribute = $this->resolveAttribute($configuration, $parameter->getClass()->getName());

        if (false === \array_key_exists($attribute, $requestParams)) {
            if (false === $parameter->allowsNull()) {
                $this->handleNotFoundError($configuration);
            }

            return null;
        }

        if (false === \is_callable($configuration->findCallback)) {
            $findCallback = [$parameter->getClass()->getName(), 'findOne'];
            $params = [[$attribute => $requestParams[$attribute]]];
        } else {
            $findCallback = $configuration->findCallback;
            $params = [$requestParams[$attribute]];
        }

        $model = \call_user_func($findCallback, ...$params);

        if (null === $model && false === $parameter->allowsNull()) {
            $this->handleNotFoundError($configuration);
        }

        if (null !== $model && false === $parameter->getClass()->isInstance($model)) {
            throw new InvalidConfigException(\sprintf(
                'Invalid data type: "%s". "%s" is expected.', \gettype($model), $parameter->getClass()->getName()
            ));
        }

        return $model;
    }

    /**
     * @param \wtfproject\yii\argumentresolver\config\ActiveRecordConfiguration $configuration
     * @param string $class
     *
     * @return string
     *
     * @throws \yii\base\NotSupportedException
     */
    protected function resolveAttribute(ActiveRecordConfiguration $configuration, string $class): string
    {
        if (null !== $configuration->attribute) {
            return $configuration->attribute;
        }

        $pk = \call_user_func([$class, 'primaryKey']);

        if (0 === \count($pk)) {
            throw new NotSupportedException('ActiveRecord model without primary key is not supported.');
        } else if (1 !== \count($pk)) {
            throw new NotSupportedException('Composite primary key is not supported for ActiveRecord resolver.');
        }

        return $pk[0];
    }

    /**
     * @param \wtfproject\yii\argumentresolver\config\ActiveRecordConfiguration $configuration
     *
     * @return void
     *
     * @throws \yii\web\NotFoundHttpException
     */
    protected function handleNotFoundError(ActiveRecordConfiguration $configuration)
    {
        if (\is_callable($configuration->notFoundCallback)) {
            \call_user_func($configuration->notFoundCallback);
        }

        throw new NotFoundHttpException();
    }
}
