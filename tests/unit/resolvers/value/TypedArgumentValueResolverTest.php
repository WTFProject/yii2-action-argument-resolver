<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\unit\resolvers\value;

use ReflectionParameter;
use wtfproject\yii\argumentresolver\exceptions\InvalidArgumentValueReceivedData;
use wtfproject\yii\argumentresolver\resolvers\value\TypedRequestAttributeValueResolver;
use wtfproject\yii\argumentresolver\tests\unit\TestCase;

/**
 * Class TypedArgumentValueResolverTest
 * @package wtfproject\yii\argumentresolver\tests\unit\resolvers\value
 *
 * @see \wtfproject\yii\argumentresolver\resolvers\value\TypedRequestAttributeValueResolver
 */
class TypedArgumentValueResolverTest extends TestCase
{
    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testSupports()
    {
        $resolver = new TypedRequestAttributeValueResolver();
        $parameterInt = new ReflectionParameter(function (int $param) {
        }, 'param');
        $parameterFloat = new ReflectionParameter(function (float $param) {
        }, 'param');
        $requestParameters = [
            'param' => 123,
        ];

        $this->assertTrue($resolver->supports($parameterInt, $requestParameters));
        $this->assertTrue($resolver->supports($parameterFloat, $requestParameters));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testDoesNotSupports()
    {
        $resolver = new TypedRequestAttributeValueResolver();
        $parameterVariadicInt = new ReflectionParameter(function (int ...$param) {
        }, 'param');
        $parameterNotTyped = new ReflectionParameter(function ($param) {
        }, 'param');
        $parameterArray = new ReflectionParameter(function ($param) {
        }, 'param');
        $parameterString = new ReflectionParameter(function (string $param) {
        }, 'param');
        $requestParameters = [];

        $this->assertFalse($resolver->supports($parameterVariadicInt, $requestParameters));
        $this->assertFalse($resolver->supports($parameterNotTyped, $requestParameters));
        $this->assertFalse($resolver->supports($parameterArray, $requestParameters));
        $this->assertFalse($resolver->supports($parameterString, $requestParameters));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testSuccessConvert()
    {
        $resolver = new TypedRequestAttributeValueResolver();
        $parameterInt = new ReflectionParameter(function (int $paramInt) {
        }, 'paramInt');
        $parameterIntNull = new ReflectionParameter(function (int $paramIntNull = null) {
        }, 'paramIntNull');
        $parameterFloat = new ReflectionParameter(function (float $paramFloat) {
        }, 'paramFloat');
        $requestParameters = [
            'paramInt' => '12312',
            'paramIntNull' => null,
            'paramFloat' => '12.033',
        ];

        $this->assertEquals(12312, $resolver->resolve($parameterInt, $requestParameters));
        $this->assertEquals(null, $resolver->resolve($parameterIntNull, $requestParameters));
        $this->assertEquals(12.033, $resolver->resolve($parameterFloat, $requestParameters));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testInvalidDataConvert()
    {
        $resolver = new TypedRequestAttributeValueResolver();
        $parameterInt = new ReflectionParameter(function (int $param) {
        }, 'param');
        $parameterInvalidFloat = new ReflectionParameter(function (float $paramFloat) {
        }, 'paramFloat');
        $requestParameters = [
            'param' => null,
            'paramFloat' => 'ewfwefwef',
        ];

        $this->expectExceptionObject(new InvalidArgumentValueReceivedData('param'));

        $resolver->resolve($parameterInt, $requestParameters);

        $this->expectExceptionObject(new InvalidArgumentValueReceivedData('paramFloat'));

        $resolver->resolve($parameterInvalidFloat, $requestParameters);
    }
}
