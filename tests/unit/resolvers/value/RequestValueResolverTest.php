<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\unit\resolvers\value;

use ReflectionParameter;
use wtfproject\yii\argumentresolver\resolvers\value\RequestValueResolver;
use wtfproject\yii\argumentresolver\tests\stubs\components\AppRequest;
use wtfproject\yii\argumentresolver\tests\unit\TestCase;
use Yii;
use yii\web\Request;

/**
 * Class RequestValueResolverTest
 * @package wtfproject\yii\argumentresolver\tests\unit\resolvers\value
 *
 * @see \wtfproject\yii\argumentresolver\resolvers\value\RequestValueResolver
 */
class RequestValueResolverTest extends TestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->mockWebApplication();
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function testSupports()
    {
        $resolver = new RequestValueResolver();

        $parameter = new ReflectionParameter(function (Request $request) {
        }, 'request');
        $parameterCustom = new ReflectionParameter(function (AppRequest $request) {
        }, 'request');
        $requestParams = [];

        $this->assertTrue(Yii::$app->has('request'));

        $this->assertTrue($resolver->supports($parameter, $requestParams));
        $this->assertTrue($resolver->supports($parameterCustom, $requestParams));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function testDoesNotSupports()
    {
        $resolver = new RequestValueResolver();

        $parameter = new ReflectionParameter(function ($param) {
        }, 'param');
        $parameterRequest = new ReflectionParameter(function (Request $request) {
        }, 'request');
        $requestParams = [];

        Yii::$app->clear('request');

        $this->assertFalse(Yii::$app->has('request'));

        $this->assertFalse($resolver->supports($parameter, $requestParams));
        $this->assertFalse($resolver->supports($parameterRequest, $requestParams));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function testResolve()
    {
        $resolver = new RequestValueResolver();

        $parameterRequest = new ReflectionParameter(function (Request $request = null) {
        }, 'request');
        $requestParams = [];

        $this->assertTrue(Yii::$app->has('request'));

        $this->assertInstanceOf(Request::class, $resolver->resolve($parameterRequest, $requestParams));

        Yii::$app->clear('request');

        $this->assertFalse(Yii::$app->has('request'));

        $this->assertNull($resolver->resolve($parameterRequest, $requestParams));
    }
}
