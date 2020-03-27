<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\filters;

use Closure;
use wtfproject\yii\argumentresolver\proxy\factory\ControllerProxyFactory;
use wtfproject\yii\argumentresolver\resolvers\ArgumentResolverInterface;
use yii\base\ActionFilter;

/**
 * Class ArgumentResolverFilter
 * @package wtfproject\yii\argumentresolver\filters
 */
class ArgumentResolverFilter extends ActionFilter
{
    use HandlesActionParamsBinding {
        bindActionParams as private;
    }

    /**
     * @var array
     */
    public $configuration = [];

    /**
     * @var \wtfproject\yii\argumentresolver\proxy\factory\ControllerProxyFactory
     */
    protected $controllerProxyFactory;

    /**
     * @var \wtfproject\yii\argumentresolver\resolvers\ArgumentResolverInterface
     */
    private $argumentResolver;

    /**
     * @var \yii\web\Controller|null
     */
    private $controller;

    /**
     * @var \yii\web\Controller|\ProxyManager\Proxy\AccessInterceptorValueHolderInterface|null
     */
    private $controllerProxy;

    /**
     * ArgumentConverterFilter constructor.
     * @param \wtfproject\yii\argumentresolver\proxy\factory\ControllerProxyFactory $controllerProxyFactory
     * @param \wtfproject\yii\argumentresolver\resolvers\ArgumentResolverInterface $argumentResolver
     * @param array $config
     */
    public function __construct(
        ControllerProxyFactory $controllerProxyFactory,
        ArgumentResolverInterface $argumentResolver,
        array $config = []
    ) {
        parent::__construct($config);

        $this->controllerProxyFactory = $controllerProxyFactory;
        $this->argumentResolver = $argumentResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        $this->controllerProxy = $this->controllerProxyFactory->createProxy(
            $action->controller,
            [
                'bindActionParams' => $this->bindActionParamsPreInterceptor(
                    Closure::fromCallable([$this, 'bindActionParams'])
                )
            ]
        );

        $this->controller = $action->controller;

        $action->controller = $this->controllerProxy;

        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     */
    public function afterAction($action, $result)
    {
        $action->controller = $this->controller;

        $this->controllerProxy = null;

        return parent::afterAction($action, $result);
    }

    /**
     * {@inheritDoc}
     */
    protected function getArgumentResolver(): ArgumentResolverInterface
    {
        return $this->argumentResolver;
    }

    /**
     * {@inheritDoc}
     */
    protected function getResolverConfiguration(): array
    {
        return $this->configuration;
    }

    /**
     * @param \Closure $replaceMethod
     *
     * @return \Closure
     */
    private function bindActionParamsPreInterceptor(Closure $replaceMethod): Closure
    {
        return function ($proxy, $instance, $method, $params, &$returnEarly) use (&$replaceMethod) {
            // set this parameter to 'true' to replace original method call
            $returnEarly = true;

            return $replaceMethod->call($this, ...\array_values($params));
        };
    }
}
