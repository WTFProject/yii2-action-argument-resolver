<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\unit\resolvers;

use ReflectionMethod;
use wtfproject\yii\argumentresolver\config\ArgumentValueResolverConfigurationInterface as Configuration;
use wtfproject\yii\argumentresolver\exceptions\ArgumentsMissingException;
use wtfproject\yii\argumentresolver\exceptions\InvalidArgumentValueReceivedData;
use wtfproject\yii\argumentresolver\exceptions\UnresolvedClassPropertyException;
use wtfproject\yii\argumentresolver\exceptions\UnsupportedArgumentTypeException;
use wtfproject\yii\argumentresolver\resolvers\ArgumentResolver;
use wtfproject\yii\argumentresolver\resolvers\value\DefaultValueResolver;
use wtfproject\yii\argumentresolver\tests\stubs\controllers\TestWebController;
use wtfproject\yii\argumentresolver\tests\stubs\fixtures\ArticleFixture;
use wtfproject\yii\argumentresolver\tests\stubs\models\Article;
use wtfproject\yii\argumentresolver\tests\unit\TestCase;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Class ArgumentResolverTest
 * @package wtfproject\yii\argumentresolver\tests\unit\resolvers
 *
 * @see \wtfproject\yii\argumentresolver\resolvers\ArgumentResolver
 * @see \wtfproject\yii\argumentresolver\tests\stubs\controllers\TestWebController
 */
class ArgumentResolverTest extends TestCase
{
    /**
     * {@inheritDoc}
     */
    public function fixtures()
    {
        return [
            ArticleFixture::class,
        ];
    }

    /**
     * @return void
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
     *
     * @see \wtfproject\yii\argumentresolver\tests\stubs\controllers\TestWebController::actionTestScalars()
     */
    public function testSuccessResolveScalars()
    {
        $controller = new TestWebController('test', Yii::$app);

        $reflectionMethod = new ReflectionMethod($controller, 'actionTestScalars');
        $requestParams = [
            'int' => '12',
            'float' => '13.598',
            'str' => 'some string',
        ];
        $configuration = [];

        $resolver = new ArgumentResolver();

        $params = $resolver->resolve($reflectionMethod, $requestParams, $configuration);

        $this->assertArrayHasKey('int', $params);
        $this->assertEquals(12, $params['int']);

        $this->assertArrayHasKey('float', $params);
        $this->assertEquals(13.598, $params['float']);

        $this->assertArrayHasKey('str', $params);
        $this->assertEquals('some string', $params['str']);

        $this->assertArrayHasKey('strDefault', $params);
        $this->assertEquals('default', $params['strDefault']);

        $this->assertArrayHasKey('intNull', $params);
        $this->assertEquals(null, $params['intNull']);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     *
     * @see \wtfproject\yii\argumentresolver\tests\stubs\controllers\TestWebController::actionTestObjects()
     */
    public function testSuccessResolveObjects()
    {
        $controller = new TestWebController('test', Yii::$app);

        $reflectionMethod = new ReflectionMethod($controller, 'actionTestObjects');
        $requestParams = [];
        $configuration = [
            'view' => [
                'class' => '\wtfproject\yii\argumentresolver\config\ComponentConfiguration',
                'component' => 'not_exist_view',
            ],
        ];

        $resolver = new ArgumentResolver();

        $this->assertTrue(Yii::$app->has('request'));

        $params = $resolver->resolve($reflectionMethod, $requestParams, $configuration);

        $this->assertArrayHasKey('request', $params);
        $this->assertInstanceOf('\yii\web\Request', $params['request']);

        $this->assertArrayHasKey('view', $params);
        $this->assertEquals(null, $params['view']);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     *
     * @see \wtfproject\yii\argumentresolver\tests\stubs\controllers\TestWebController::actionTestMixed()
     */
    public function testSuccessResolveMixed()
    {
        $this->loadFixtures();

        $controller = new TestWebController('test', Yii::$app);

        $reflectionMethod = new ReflectionMethod($controller, 'actionTestMixed');
        $requestParams = [
            'bool' => 'false',
            'arr' => ['12', '14', 'str'],
            'id' => '1',
            'param' => 'some string',
        ];
        $configuration = [];

        $resolver = new ArgumentResolver();

        $params = $resolver->resolve($reflectionMethod, $requestParams, $configuration);

        $this->assertArrayHasKey('bool', $params);
        $this->assertEquals(false, $params['bool']);

        $this->assertArrayHasKey('arr', $params);
        $this->assertTrue(\is_array($params['arr']));

        $this->assertArrayHasKey('model', $params);
        $this->assertInstanceOf(Article::class, $params['model']);
        $this->assertEquals(1, $params['model']->id);

        $this->assertArrayHasKey('param', $params);
        $this->assertEquals('some string', $params['param']);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     *
     * @see \wtfproject\yii\argumentresolver\tests\stubs\controllers\TestWebController::actionTestVariadic()
     */
    public function testVariadicArgumentsNotSupported()
    {
        $controller = new TestWebController('test', Yii::$app);

        $reflectionMethod = new ReflectionMethod($controller, 'actionTestVariadic');
        $requestParams = [];
        $configuration = [];

        $resolver = new ArgumentResolver();

        $this->expectException(UnsupportedArgumentTypeException::class);

        $resolver->resolve($reflectionMethod, $requestParams, $configuration);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     *
     * @see \wtfproject\yii\argumentresolver\tests\stubs\controllers\TestWebController::actionTestMixed()
     */
    public function testInvalidConfigurationClassProvided()
    {
        $controller = new TestWebController('test', Yii::$app);

        $reflectionMethod = new ReflectionMethod($controller, 'actionTestMixed');
        $requestParams = [];
        $configuration = [
            'bool' => [
                'class' => 'yii\web\Request',
            ],
        ];

        $resolver = new ArgumentResolver();

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Invalid data type: yii\web\Request. ' . Configuration::class . ' is expected.');

        $resolver->resolve($reflectionMethod, $requestParams, $configuration);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     *
     * @see \wtfproject\yii\argumentresolver\tests\stubs\controllers\TestWebController::actionTestObject()
     */
    public function testClassObjectNotResolved()
    {
        $controller = new TestWebController('test', Yii::$app);

        $reflectionMethod = new ReflectionMethod($controller, 'actionTestObject');
        $requestParams = [];
        $configuration = [];

        $resolver = new ArgumentResolver();

        $this->expectException(UnresolvedClassPropertyException::class);

        $resolver->resolve($reflectionMethod, $requestParams, $configuration);
    }

    /**
     * Test situation, then scalar value can not be resolved
     * This can happen, if no resolver found for argument
     *
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     *
     * @see \wtfproject\yii\argumentresolver\tests\stubs\controllers\TestWebController::actionTestScalars()
     */
    public function testArgumentInvalidDataReceived()
    {
        $controller = new TestWebController('test', Yii::$app);

        $actionReflection = new ReflectionMethod($controller, 'actionTestScalars');
        $requestParams = [
            'int' => '12',
        ];
        $configuration = [];

        // disable all resolvers to trigger exceptional situation
        $resolver = new ArgumentResolver([DefaultValueResolver::class]);

        $this->expectExceptionObject(new InvalidArgumentValueReceivedData('int'));

        $resolver->resolve($actionReflection, $requestParams, $configuration);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     *
     * @see \wtfproject\yii\argumentresolver\tests\stubs\controllers\TestWebController::actionTestScalars()
     */
    public function testResolveFailedMissingParams()
    {
        $controller = new TestWebController('test', Yii::$app);

        $reflectionMethod = new ReflectionMethod($controller, 'actionTestScalars');
        $requestParams = [];
        $configuration = [];

        $resolver = new ArgumentResolver();

        $this->expectExceptionObject(new ArgumentsMissingException([
            'int', 'float', 'str',
        ]));

        $resolver->resolve($reflectionMethod, $requestParams, $configuration);
    }
}
