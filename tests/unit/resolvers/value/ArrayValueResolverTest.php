<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\unit\resolvers\value;

use ReflectionParameter;
use wtfproject\yii\argumentresolver\resolvers\value\ArrayValueResolver;
use wtfproject\yii\argumentresolver\tests\unit\TestCase;

/**
 * Class ArrayValueResolverTest
 * @package wtfproject\yii\argumentresolver\tests\unit\resolvers\value
 *
 * @see \wtfproject\yii\argumentresolver\resolvers\value\ArrayValueResolver
 */
class ArrayValueResolverTest extends TestCase
{
    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testSupportsParameter()
    {
        $resolver = new ArrayValueResolver();
        $parameter = new ReflectionParameter(function (array $param) {
        }, 'param');
        $requestParameters = [
            'param' => [],
        ];

        $this->assertTrue($resolver->supports($parameter, $requestParameters));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testDoesNotSupportsParameter()
    {
        $resolver = new ArrayValueResolver();
        $parameterVariadic = new ReflectionParameter(function (array ...$param) {
        }, 'param');
        $parameterScalar = new ReflectionParameter(function ($param) {
        }, 'param');
        $parameterClass = new ReflectionParameter(function (ReflectionParameter $param) {
        }, 'param');
        $requestParameters = [];

        $this->assertFalse($resolver->supports($parameterVariadic, $requestParameters));
        $this->assertFalse($resolver->supports($parameterScalar, $requestParameters));
        $this->assertFalse($resolver->supports($parameterClass, $requestParameters));
    }
}
