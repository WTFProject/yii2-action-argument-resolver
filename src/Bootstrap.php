<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver;

use Closure;
use ProxyManager\Configuration;
use ProxyManager\FileLocator\FileLocator;
use ProxyManager\GeneratorStrategy\FileWriterGeneratorStrategy;
use wtfproject\yii\argumentresolver\proxy\factory\ControllerProxyFactory;
use wtfproject\yii\argumentresolver\resolvers\ArgumentResolver;
use wtfproject\yii\argumentresolver\resolvers\ArgumentResolverInterface;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Configurable;
use yii\base\InvalidConfigException;

/**
 * Class Bootstrap
 * @package wtfproject\yii\argumentresolver
 */
final class Bootstrap implements BootstrapInterface, Configurable
{
    /**
     * @var bool
     */
    public $enableProxyCache = false;

    /**
     * @var string
     */
    public $proxyCachePath = '@runtime/proxy';

    /**
     * Bootstrap constructor.
     * @param array $config
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct(array $config = [])
    {
        Yii::configure($this, $config);

        if ($this->enableProxyCache && (false === \is_string($this->proxyCachePath) || '' === $this->proxyCachePath)) {
            throw new InvalidConfigException('Parameter "proxyCachePath" must be not empty string.');
        }

        if ($this->enableProxyCache && 0 === \strpos($this->proxyCachePath, '@')) {
            $this->proxyCachePath = Yii::getAlias($this->proxyCachePath);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        $container = Yii::$container;

        $container->setSingleton(
            ControllerProxyFactory::class,
            Closure::fromCallable([$this, 'createProxyFactoryCallback'])
        );

        if (false === $container->hasSingleton(ArgumentResolverInterface::class)) {
            $container->setSingleton(ArgumentResolverInterface::class, [
                'class' => ArgumentResolver::class,
            ]);
        }
    }

    /**
     * @return \wtfproject\yii\argumentresolver\proxy\factory\ControllerProxyFactory
     */
    private function createProxyFactoryCallback(): ControllerProxyFactory
    {
        $configuration = $this->enableProxyCache ? $this->createProxyConfiguration() : null;

        return new ControllerProxyFactory($configuration);
    }

    /**
     *
     * @return \ProxyManager\Configuration
     */
    private function createProxyConfiguration(): Configuration
    {
        $proxyCachePath = Yii::getAlias($this->proxyCachePath);

        $configuration = new Configuration();

        $fileLocator = new FileLocator($proxyCachePath);
        $generatorStrategy = new FileWriterGeneratorStrategy($fileLocator);

        $configuration->setGeneratorStrategy($generatorStrategy);
        $configuration->setProxiesTargetDir($proxyCachePath);

        \spl_autoload_register($configuration->getProxyAutoloader());

        return $configuration;
    }
}
