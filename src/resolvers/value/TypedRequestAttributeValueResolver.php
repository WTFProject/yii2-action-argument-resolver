<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\resolvers\value;

use ReflectionParameter;
use ReflectionType;
use wtfproject\yii\argumentresolver\config\ArgumentValueResolverConfigurationInterface as Configuration;
use wtfproject\yii\argumentresolver\exceptions\InvalidArgumentValueReceivedData;

/**
 * Class TypedRequestAttributeValueResolver
 * @package wtfproject\yii\argumentresolver\resolvers\value
 */
class TypedRequestAttributeValueResolver implements ArgumentValueResolverInterface
{
    /**
     * {@inheritDoc}
     */
    public function supports(
        ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null
    ): bool {
        return \array_key_exists($parameter->getName(), $requestParams)
            && null !== ($type = $parameter->getType())
            && $type->isBuiltin()
            // if parameter null and type not allows null
            // resolver will throw an error about invalid received data
            && (null !== $requestParams[$parameter->getName()] || false === $type->allowsNull());
    }

    /**
     * {@inheritDoc}
     *
     * @return int|float
     *
     * @throws \wtfproject\yii\argumentresolver\exceptions\InvalidArgumentValueReceivedData
     */
    public function resolve(ReflectionParameter $parameter, array &$requestParams, Configuration $configuration = null)
    {
        $typeName = $this->getParameterTypeName($parameter->getType());

        if ('float' === $typeName) {
            $value = \filter_var($requestParams[$parameter->getName()], \FILTER_VALIDATE_FLOAT, \FILTER_NULL_ON_FAILURE);
        } else if ('int' === $typeName) {
            $value = \filter_var($requestParams[$parameter->getName()], \FILTER_VALIDATE_INT, \FILTER_NULL_ON_FAILURE);
        } else if ('bool' === $typeName) {
            $value = \filter_var($requestParams[$parameter->getName()], \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE);
        } else if ('array' === $typeName) {
            $value = (array)$requestParams[$parameter->getName()];
        } else if ('string' === $typeName && false === \is_array($requestParams[$parameter->getName()])) {
            $value = $requestParams[$parameter->getName()];
        } else {
            $value = null;
        }

        if (null === $value) {
            throw new InvalidArgumentValueReceivedData($parameter->getName());
        }

        return $value;
    }

    /**
     * @param \ReflectionType $type
     *
     * @return string
     */
    private function getParameterTypeName(ReflectionType $type): string
    {
        return \PHP_VERSION_ID >= 70100 ? $type->getName() : (string)$type;
    }
}
