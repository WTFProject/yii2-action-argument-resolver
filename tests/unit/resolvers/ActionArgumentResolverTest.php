<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\unit\resolvers;

use ReflectionMethod;
use wtfproject\yii\argumentresolver\exceptions\ArgumentsMissingException;
use wtfproject\yii\argumentresolver\exceptions\InvalidArgumentValueReceivedData;
use wtfproject\yii\argumentresolver\resolvers\ArgumentResolver;
use wtfproject\yii\argumentresolver\tests\stubs\TestWebController;
use wtfproject\yii\argumentresolver\tests\unit\TestCase;
use Yii;

/**
 * Class ActionArgumentResolverTest
 * @package wtfproject\yii\argumentresolver\tests\unit\resolvers
 *
 * @see \wtfproject\yii\argumentresolver\resolvers\ArgumentResolver
 */
class ActionArgumentResolverTest extends TestCase
{
    /**
     * @return void
     *
     * @throws \yii\base\Exception
     */
    public function setUp()
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
     * @see \wtfproject\yii\argumentresolver\tests\stubs\TestWebController::actionTestResolver()
     */
    public function testSuccessResolve()
    {
        $controller = new TestWebController('test', Yii::$app);

        $actionReflection = new ReflectionMethod($controller, 'actionTestResolver');

        $requestParams = [
            'intArg' => '23',
            'stringArg' => 'some string',
            'floatArg' => '23.56',
            'arrayArg' => ['12', 13, '36'],
            'simpleArg' => 'argument',
        ];
        $hints = [

        ];

        $resolver = new ArgumentResolver();

        $arguments = $resolver->resolve($actionReflection, $requestParams, $hints);

        $this->assertArrayHasKey('intArg', $arguments);
        $this->assertEquals(23, $arguments['intArg']);

        $this->assertArrayHasKey('stringArg', $arguments);
        $this->assertEquals('some string', $arguments['stringArg']);

        $this->assertArrayHasKey('floatArg', $arguments);
        $this->assertEquals(23.56, $arguments['floatArg']);

        $this->assertArrayHasKey('arrayArg', $arguments);
        $this->assertTrue(\is_array($arguments['arrayArg']));

        $this->assertArrayHasKey('simpleArg', $arguments);
        $this->assertEquals('argument', $arguments['simpleArg']);

        $this->assertArrayHasKey('intArgDefault', $arguments);
        $this->assertEquals(26, $arguments['intArgDefault']);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     *
     * @see \wtfproject\yii\argumentresolver\tests\stubs\TestWebController::actionTestResolver()
     */
    public function testResolveFailedMissingParams()
    {
        $controller = new TestWebController('test', Yii::$app);

        $actionReflection = new ReflectionMethod($controller, 'actionTestResolver');

        $requestParams = [
            'intArg' => '23',
            'floatArg' => '23.56',
            'simpleArg' => 'argument',
        ];
        $hints = [

        ];

        $resolver = new ArgumentResolver();

        $this->expectExceptionObject(new ArgumentsMissingException([
            'stringArg', 'arrayArg',
        ]));

        $resolver->resolve($actionReflection, $requestParams, $hints);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     *
     * @see \wtfproject\yii\argumentresolver\tests\stubs\TestWebController::actionTestResolver()
     */
    public function testResolveFailedInvalidReceivedData()
    {
        $controller = new TestWebController('test', Yii::$app);

        $actionReflection = new ReflectionMethod($controller, 'actionTestResolver');

        $requestParams = [
            'intArg' => '23',
            'stringArg' => 'some string',
            'floatArg' => '23.56',
            'arrayArg' => ['12', 13, '36'],
            'simpleArg' => ['argument'], //invalid parameter
        ];
        $hints = [

        ];

        $resolver = new ArgumentResolver();

        $this->expectExceptionObject(new InvalidArgumentValueReceivedData('simpleArg'));

        $resolver->resolve($actionReflection, $requestParams, $hints);
    }
}
