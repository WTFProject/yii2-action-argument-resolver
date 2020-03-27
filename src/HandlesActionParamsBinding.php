<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver;

use ReflectionMethod;
use wtfproject\yii\argumentresolver\exceptions\ArgumentsMissingException;
use wtfproject\yii\argumentresolver\exceptions\InvalidArgumentValueReceivedData;
use wtfproject\yii\argumentresolver\resolvers\ArgumentResolverInterface;
use Yii;
use yii\base\InlineAction;
use yii\web\BadRequestHttpException;

/**
 * Trait HandlesActionParamsBinding
 * @package wtfproject\yii\argumentresolver
 */
trait HandlesActionParamsBinding
{
    /**
     * @param \yii\base\Action $action
     * @param array $params
     *
     * @return array
     *
     * @throws \ReflectionException
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\base\InvalidConfigException
     *
     * @see \yii\web\Controller::bindActionParams()
     */
    public function bindActionParams($action, $params)
    {
        if ($action instanceof InlineAction) {
            $method = new ReflectionMethod($action->controller, $action->actionMethod);
        } else {
            $method = new ReflectionMethod($action, 'run');
        }

        $configuration = $this->getResolverConfiguration()[$action->id] ?? [];

        try {
            $arguments = $this->getArgumentResolver()->resolve($method, $params, $configuration);
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

    /**
     * @return \wtfproject\yii\argumentresolver\resolvers\ArgumentResolverInterface
     *
     * @throws \yii\base\InvalidConfigException
     */
    protected function getArgumentResolver(): ArgumentResolverInterface
    {
        return Yii::createObject(ArgumentResolverInterface::class);
    }

    /**
     * @return array
     */
    protected function getResolverConfiguration(): array
    {
        return [];
    }
}
