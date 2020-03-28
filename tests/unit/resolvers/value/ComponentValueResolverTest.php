<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\unit\resolvers\value;

use ReflectionParameter;
use wtfproject\yii\argumentresolver\config\ArgumentValueResolverConfigurationInterface;
use wtfproject\yii\argumentresolver\config\ComponentConfiguration as Configuration;
use wtfproject\yii\argumentresolver\resolvers\value\ComponentValueResolver;
use wtfproject\yii\argumentresolver\tests\unit\TestCase;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Request;
use yii\web\View;

/**
 * Class ComponentValueResolverTest
 * @package wtfproject\yii\argumentresolver\tests\unit\resolvers\value
 *
 * @see \wtfproject\yii\argumentresolver\resolvers\value\ComponentValueResolver
 */
class ComponentValueResolverTest extends TestCase
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
        $resolver = new ComponentValueResolver();

        $parameter = new ReflectionParameter(function (View $view) {
        }, 'view');
        $requestParams = [];
        $configuration = new Configuration(['component' => 'view']);

        $this->assertTrue($resolver->supports($parameter, $requestParams, $configuration));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function testDoesNotSupports()
    {
        $resolver = new ComponentValueResolver();

        $parameter = new ReflectionParameter(function (View $view) {
        }, 'view');
        $parameterScalar = new ReflectionParameter(function ($param) {
        }, 'param');
        $requestParams = [];
        $configuration = new Configuration(['component' => 'view']);
        $customConfiguration = new class implements ArgumentValueResolverConfigurationInterface {
        };

        $this->assertFalse($resolver->supports($parameter, $requestParams));
        $this->assertFalse($resolver->supports($parameter, $requestParams, $customConfiguration));
        $this->assertFalse($resolver->supports($parameterScalar, $requestParams, $configuration));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function testSuccessResolve()
    {
        $resolver = new ComponentValueResolver();

        $parameter = new ReflectionParameter(function (View $view) {
        }, 'view');
        $requestParams = [];

        $this->assertTrue(Yii::$app->has('view'));
        $this->assertTrue(Yii::$app->getModule('test')->has('view'));
        $this->assertTrue(Yii::$app->getModule('test/test')->has('view'));

        $view = $resolver->resolve($parameter, $requestParams, new Configuration(['component' => 'view']));

        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('app_view', $view->title);

        $view = $resolver->resolve($parameter, $requestParams, new Configuration([
            'currentModule' => Yii::$app->getModule('test'),
            'component' => 'view',
        ]));

        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('test_module_view', $view->title);

        $view = $resolver->resolve($parameter, $requestParams, new Configuration([
            'module' => 'test/test',
            'component' => 'view',
        ]));

        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('test_embed_module_view', $view->title);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function testSuccessResolveNotExistNullableComponent()
    {
        $resolver = new ComponentValueResolver();

        $parameter = new ReflectionParameter(function (View $view = null) {
        }, 'view');
        $requestParams = [];
        $configuration = new Configuration(['component' => 'not_exist_view']);

        $this->assertFalse(Yii::$app->has('not_exist_view'));

        $this->assertNull($resolver->resolve($parameter, $requestParams, $configuration));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function testFailedResolveNotExistComponent()
    {
        $resolver = new ComponentValueResolver();

        $parameter = new ReflectionParameter(function (View $view) {
        }, 'view');
        $requestParams = [];
        $configuration = new Configuration(['component' => 'not_exist_view']);

        $this->assertFalse(Yii::$app->has('not_exist_view'));

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Can not retrieve component "not_exist_view" from "application".');

        $resolver->resolve($parameter, $requestParams, $configuration);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function testFailedResolveInvalidModule()
    {
        $resolver = new ComponentValueResolver();

        $parameter = new ReflectionParameter(function (View $view = null) {
        }, 'view');
        $requestParams = [];

        $this->assertFalse(Yii::$app->hasModule('not_exist_module'));
        $this->assertFalse(Yii::$app->hasModule('test/not_exist_module'));

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Can not retrieve module "not_exist_module" from "application".');

        $resolver->resolve($parameter, $requestParams, new Configuration([
            'module' => 'not_exist_module',
            'component' => 'view',
        ]));

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Can not retrieve module "not_exist_module" from "test" module.');

        $resolver->resolve($parameter, $requestParams, new Configuration([
            'currentModule' => Yii::$app->getModule('test'),
            'module' => 'not_exist_module',
            'component' => 'view',
        ]));

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Can not retrieve module "test/not_exist_module" from "application".');

        $resolver->resolve($parameter, $requestParams, new Configuration([
            'module' => 'test/not_exist_module',
            'component' => 'view',
        ]));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function testFailedResolveInvalidComponentType()
    {
        $resolver = new ComponentValueResolver();

        $parameter = new ReflectionParameter(function (View $view) {
        }, 'view');
        $requestParams = [];
        $configuration = new Configuration(['component' => 'request']);

        $this->assertTrue(Yii::$app->has('request'));
        $this->assertInstanceOf(Request::class, Yii::$app->get('request'));

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage(
            "\"{$configuration->component}\" refers to a " . Request::class . ' component. ' . View::class . ' is expected.'
        );

        $resolver->resolve($parameter, $requestParams, $configuration);
    }
}
