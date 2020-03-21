<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\resolvers;

use ReflectionMethod;
use ReflectionParameter;
use wtfproject\yii\argumentresolver\exceptions\ArgumentConfigurationMissingException;
use wtfproject\yii\argumentresolver\exceptions\ArgumentsMissingException;
use wtfproject\yii\argumentresolver\exceptions\InvalidArgumentValueReceivedData;
use wtfproject\yii\argumentresolver\resolvers\value\ArgumentValueResolverInterface;
use wtfproject\yii\argumentresolver\resolvers\value\ArrayArgumentValueResolver;
use wtfproject\yii\argumentresolver\resolvers\value\ComponentArgumentValueResolver;
use wtfproject\yii\argumentresolver\resolvers\value\DefaultArgumentValueResolver;
use wtfproject\yii\argumentresolver\resolvers\value\RequestArgumentValueResolver;
use wtfproject\yii\argumentresolver\resolvers\value\TypedArgumentValueResolver;
use Yii;

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
                ArrayArgumentValueResolver::class,
                TypedArgumentValueResolver::class,
                RequestArgumentValueResolver::class,
                ComponentArgumentValueResolver::class,
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
            foreach ($this->getResolvers() as $resolver) {
                $resolverConfiguration = $this->getResolverConfiguration($resolver, $parameter, $configuration);

                if (false === $resolver->supports($parameter, $requestParams, $resolverConfiguration)) {
                    continue;
                }

                $arguments[$parameter->getName()] = $resolver->resolve(
                    $parameter, $requestParams, $resolverConfiguration
                );

                continue(2);
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
     * @param \wtfproject\yii\argumentresolver\resolvers\value\ArgumentValueResolverInterface $resolver
     * @param \ReflectionParameter $parameter
     * @param array &$configuration
     *
     * @return \wtfproject\yii\argumentresolver\config\ArgumentValueResolverConfigurationInterface|null
     *
     * @throws \yii\base\InvalidConfigException
     */
    private function getResolverConfiguration(
        ArgumentValueResolverInterface $resolver, ReflectionParameter $parameter, array &$configuration = []
    ) {
        $configurationClass = $resolver->getConfigurationClass();

        if (null === $configurationClass) {
            return null;
        }

        if (false === \array_key_exists($parameter->getName(), $configuration)) {
            throw new ArgumentConfigurationMissingException(
                \sprintf('Configuration for argument "%s" is missing.', $parameter->getName())
            );
        }

        return Yii::createObject(\array_merge($configuration[$parameter->getName()], [
            'class' => $configurationClass,
        ]));
    }
}
