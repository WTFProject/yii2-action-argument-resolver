<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\unit\resolvers\value;

use ReflectionFunction;
use ReflectionParameter;
use wtfproject\yii\argumentresolver\exceptions\InvalidArgumentValueReceivedData;
use wtfproject\yii\argumentresolver\resolvers\value\TypedRequestAttributeValueResolver;
use wtfproject\yii\argumentresolver\tests\unit\TestCase;
use yii\helpers\ArrayHelper;

/**
 * Class TypedRequestAttributeValueResolverTest
 * @package wtfproject\yii\argumentresolver\tests\unit\resolvers\value
 *
 * @see \wtfproject\yii\argumentresolver\resolvers\value\TypedRequestAttributeValueResolver
 */
class TypedRequestAttributeValueResolverTest extends TestCase
{
    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testSupports()
    {
        $resolver = new TypedRequestAttributeValueResolver();
        $reflectionFunction = new ReflectionFunction(function (
            int $int, float $float, bool $bool, string $str, array $arr
        ) {
        });
        $requestParameters = [
            'int' => '123',
            'float' => '123.23',
            'bool' => '1',
            'str' => 'afaas',
            'arr' => ['123', '3434'],
        ];

        $parameters = ArrayHelper::index($reflectionFunction->getParameters(), function (ReflectionParameter $parameter) {
            return $parameter->getName();
        });

        $this->assertTrue($resolver->supports($parameters['int'], $requestParameters));
        $this->assertTrue($resolver->supports($parameters['float'], $requestParameters));
        $this->assertTrue($resolver->supports($parameters['bool'], $requestParameters));
        $this->assertTrue($resolver->supports($parameters['str'], $requestParameters));
        $this->assertTrue($resolver->supports($parameters['arr'], $requestParameters));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testDoesNotSupports()
    {
        $resolver = new TypedRequestAttributeValueResolver();

        $parameterNotTyped = new ReflectionParameter(function ($param) {
        }, 'param');
        $parameterClass = new ReflectionParameter(function (ReflectionParameter $class) {
        }, 'class');
        $parameterStringNotExist = new ReflectionParameter(function (string $param) {
        }, 'param');
        $requestParameters = [];

        $this->assertFalse($resolver->supports($parameterNotTyped, $requestParameters));
        $this->assertFalse($resolver->supports($parameterClass, $requestParameters));
        $this->assertFalse($resolver->supports($parameterStringNotExist, $requestParameters));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testSuccessConvert()
    {
        $resolver = new TypedRequestAttributeValueResolver();
        $parameterInt = new ReflectionParameter(function (int $int) {
        }, 'int');
        $parameterFloat = new ReflectionParameter(function (float $float) {
        }, 'float');
        $parameterBool = new ReflectionParameter(function (bool $bool) {
        }, 'bool');
        $parameterString = new ReflectionParameter(function (string $str) {
        }, 'str');
        $parameterArr = new ReflectionParameter(function (array $arr) {
        }, 'arr');

        $requestParameters = [
            'int' => '12312',
            'float' => '12.033',
            'bool' => '1',
            'str' => 'some_str',
            'arr' => ['123123', '345345'],
        ];


        $this->assertEquals(12312, $resolver->resolve($parameterInt, $requestParameters));
        $this->assertEquals(12.033, $resolver->resolve($parameterFloat, $requestParameters));
        $this->assertEquals(true, $resolver->resolve($parameterBool, $requestParameters));
        $this->assertEquals('some_str', $resolver->resolve($parameterString, $requestParameters));
        $this->assertEquals(['123123', '345345'], $resolver->resolve($parameterArr, $requestParameters));

        $requestParameters['arr'] = null;

        $this->assertEquals([], $resolver->resolve($parameterArr, $requestParameters));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testInvalidDataConvert()
    {
        $resolver = new TypedRequestAttributeValueResolver();

        $parameterInt = new ReflectionParameter(function (int $int) {
        }, 'int');
        $parameterFloat = new ReflectionParameter(function (float $float) {
        }, 'float');
        $parameterString = new ReflectionParameter(function (string $str) {
        }, 'str');
        $requestParameters = [
            'int' => null,
            'float' => 'ewfwefwef',
            'str' => [],
        ];

        $this->expectExceptionObject(new InvalidArgumentValueReceivedData('int'));
        $resolver->resolve($parameterInt, $requestParameters);

        $this->expectExceptionObject(new InvalidArgumentValueReceivedData('float'));
        $resolver->resolve($parameterFloat, $requestParameters);

        $this->expectExceptionObject(new InvalidArgumentValueReceivedData('str'));
        $resolver->resolve($parameterString, $requestParameters);
    }
}
