<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\filters;

use Closure;
use ReflectionMethod;
use wtfproject\yii\argumentresolver\exceptions\ArgumentsMissingException;
use wtfproject\yii\argumentresolver\exceptions\InvalidArgumentValueReceivedData;
use wtfproject\yii\argumentresolver\proxy\factory\ControllerProxyFactory;
use wtfproject\yii\argumentresolver\resolvers\ArgumentResolverInterface;
use Yii;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\base\InlineAction;
use yii\web\BadRequestHttpException;

/**
 * Class ArgumentResolverFilter
 * @package wtfproject\yii\argumentresolver\filters
 */
class ArgumentResolverFilter extends ActionFilter
{
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
                    Closure::fromCallable([$this, 'bindActionParameters'])
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

    /**
     * @param \yii\base\Action $action
     * @param array $params
     *
     * @return array
     *
     * @throws \ReflectionException
     * @throws \yii\web\BadRequestHttpException
     */
    private function bindActionParameters(Action $action, array $params)
    {
        if ($action instanceof InlineAction) {
            $method = new ReflectionMethod($action->controller, $action->actionMethod);
        } else {
            $method = new ReflectionMethod($action, 'run');
        }

        $configuration = $this->configuration[$action->id] ?? [];

        try {
            $arguments = $this->argumentResolver->resolve($method, $params, $configuration);
        } catch (InvalidArgumentValueReceivedData $exception) {
            throw new BadRequestHttpException(Yii::t('yii', 'Invalid data received for parameter "{param}".', [
                'param' => $exception->getParameter(),
            ]), 0, $exception);
        } catch (ArgumentsMissingException $exception) {
            throw new BadRequestHttpException(Yii::t('yii', 'Missing required parameters: {params}', [
                'params' => \implode(', ', $exception->getMissing()),
            ]));
        }

        $action->controller->actionParams = $arguments;

        return \array_values($arguments);
    }
}
