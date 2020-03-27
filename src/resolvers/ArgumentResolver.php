<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\resolvers;

use ReflectionMethod;
use ReflectionParameter;
use wtfproject\yii\argumentresolver\config\ArgumentValueResolverConfigurationInterface as Configuration;
use wtfproject\yii\argumentresolver\exceptions\ArgumentsMissingException;
use wtfproject\yii\argumentresolver\exceptions\InvalidArgumentValueReceivedData;
use wtfproject\yii\argumentresolver\exceptions\UnresolvedClassPropertyException;
use wtfproject\yii\argumentresolver\resolvers\value\ActiveRecordArgumentValueResolver;
use wtfproject\yii\argumentresolver\resolvers\value\ArrayArgumentValueResolver;
use wtfproject\yii\argumentresolver\resolvers\value\ComponentArgumentValueResolver;
use wtfproject\yii\argumentresolver\resolvers\value\DefaultArgumentValueResolver;
use wtfproject\yii\argumentresolver\resolvers\value\RequestArgumentValueResolver;
use wtfproject\yii\argumentresolver\resolvers\value\TypedArgumentValueResolver;
use Yii;
use yii\di\Instance;

/**
 * Class ArgumentResolver
 * @package wtfproject\yii\argumentresolver\resolvers
 *
 * @internal
 */
final class ArgumentResolver implements ArgumentResolverInterface
{
    /**
     * @var \wtfproject\yii\argumentresolver\resolvers\value\ArgumentValueResolverInterface[]
     */
    private $resolvers;

    /**
     * ArgumentResolver constructor.
     *
     * @param array|null $resolvers
     */
    public function __construct(array $resolvers = null)
    {
        $this->resolvers = $resolvers ?? [
                ActiveRecordArgumentValueResolver::class,
                ComponentArgumentValueResolver::class,
                ArrayArgumentValueResolver::class,
                TypedArgumentValueResolver::class,
                RequestArgumentValueResolver::class,
                DefaultArgumentValueResolver::class,
            ];
    }

    /**
     * {@inheritDoc}
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function resolve(ReflectionMethod $actionMethod, array &$requestParams, array &$configuration = []): array
    {
        $arguments = $missing = [];

        foreach ($actionMethod->getParameters() as $parameter) {
            if ($parameter->isVariadic()) {
                //TODO: variadic properties is not supported.
                throw new \RuntimeException();
            }

            $parameterConfiguration = $this->getParameterConfiguration($parameter, $configuration);

            foreach ($this->getResolvers() as $resolver) {
                if (false === $resolver->supports($parameter, $requestParams, $parameterConfiguration)) {
                    continue;
                }

                $arguments[$parameter->getName()] = $resolver->resolve(
                    $parameter, $requestParams, $parameterConfiguration
                );

                continue 2;
            }

            if (null !== $parameter->getClass()) {
                //TODO: add message
                throw new UnresolvedClassPropertyException();
            }

            if (\array_key_exists($parameter->getName(), $requestParams)) {
                throw new InvalidArgumentValueReceivedData($parameter->getName());
            }

            $missing[] = $parameter->getName();
        }

        if (0 !== \count($missing)) {
            throw new ArgumentsMissingException($missing);
        }

        return $arguments;
    }

    /**
     * @return \wtfproject\yii\argumentresolver\resolvers\value\ArgumentValueResolverInterface[]
     *
     * @throws \yii\base\InvalidConfigException
     */
    private function getResolvers(): array
    {
        foreach ($this->resolvers as &$resolver) {
            if (false === \is_object($resolver)) {
                $resolver = Yii::createObject($resolver);
            }
        }

        return $this->resolvers;
    }

    /**
     * @param \ReflectionParameter $parameter
     * @param array &$configuration
     *
     * @return \wtfproject\yii\argumentresolver\config\ArgumentValueResolverConfigurationInterface|null
     *
     * @throws \yii\base\InvalidConfigException
     */
    private function getParameterConfiguration(ReflectionParameter $parameter, array &$configuration = [])
    {
        if (false === \array_key_exists($parameter->getName(), $configuration)) {
            return null;
        }

        return Instance::ensure($configuration[$parameter->getName()], Configuration::class);
    }
}
