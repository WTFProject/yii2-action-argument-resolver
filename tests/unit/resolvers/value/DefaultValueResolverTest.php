<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\unit\resolvers\value;

use ReflectionParameter;
use wtfproject\yii\argumentresolver\resolvers\value\DefaultValueResolver;
use wtfproject\yii\argumentresolver\tests\unit\TestCase;

/**
 * Class DefaultValueResolverTest
 * @package wtfproject\yii\argumentresolver\tests\unit\resolvers\value
 *
 * @see \wtfproject\yii\argumentresolver\resolvers\value\DefaultValueResolver
 */
class DefaultValueResolverTest extends TestCase
{
    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testSupports()
    {
        $resolver = new DefaultValueResolver();

        $parameterInt = new ReflectionParameter(function (int $int = 12) {
        }, 'int');
        $parameterNull = new ReflectionParameter(function ($null = null) {
        }, 'null');
        $requestParameters = [];

        $this->assertTrue($resolver->supports($parameterInt, $requestParameters));
        $this->assertTrue($resolver->supports($parameterNull, $requestParameters));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testDoesNotSupports()
    {
        $resolver = new DefaultValueResolver();

        $parameter = new ReflectionParameter(function ($param) {
        }, 'param');
        $parameterNull = new ReflectionParameter(function ($null = null) {
        }, 'null');
        $requestParameters = [
            'null' => 'val',
        ];

        $this->assertFalse($resolver->supports($parameter, $requestParameters));
        $this->assertFalse($resolver->supports($parameterNull, $requestParameters));
    }
}
