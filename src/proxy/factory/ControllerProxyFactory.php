<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\proxy\factory;

use ProxyManager\Configuration;
use ProxyManager\Factory\AbstractBaseFactory;
use ProxyManager\Proxy\AccessInterceptorValueHolderInterface;
use ProxyManager\ProxyGenerator\ProxyGeneratorInterface;
use wtfproject\yii\argumentresolver\proxy\generators\AccessInterceptorValueHolderGenerator;
use yii\base\Controller;

/**
 * Class ControllerProxyFactory
 * @package wtfproject\yii\argumentresolver\proxy\factory
 */
final class ControllerProxyFactory extends AbstractBaseFactory
{
    /**
     * @var \wtfproject\yii\argumentresolver\proxy\generators\AccessInterceptorValueHolderGenerator
     */
    private $generator;

    /**
     * AccessInterceptorValueHolderFactory constructor.
     * @param \ProxyManager\Configuration|null $configuration
     */
    public function __construct(Configuration $configuration = null)
    {
        parent::__construct($configuration);

        $this->generator = new AccessInterceptorValueHolderGenerator([
            'bindActionParams',
        ]);
    }

    /**
     * @param \yii\base\Controller $instance
     * @param array $prefixInterceptors
     * @param array $suffixInterceptors
     *
     * @return \ProxyManager\Proxy\AccessInterceptorValueHolderInterface
     */
    public function createProxy(
        Controller $instance, array $prefixInterceptors = [], array $suffixInterceptors = []
    ): AccessInterceptorValueHolderInterface {
        $proxyClassName = $this->generateProxy(\get_class($instance));

        return $proxyClassName::staticProxyConstructor($instance, $prefixInterceptors, $suffixInterceptors);
    }

    /**
     * {@inheritDoc}
     */
    protected function getGenerator(): ProxyGeneratorInterface
    {
        return $this->generator;
    }
}
