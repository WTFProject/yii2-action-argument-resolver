<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\unit\resolvers\value;

use ReflectionParameter;
use wtfproject\yii\argumentresolver\resolvers\value\RequestAttributeValueResolver;
use wtfproject\yii\argumentresolver\tests\unit\TestCase;

/**
 * Class RequestAttributeValueResolverTest
 * @package wtfproject\yii\argumentresolver\tests\unit\converters\scalar
 *
 * @see \wtfproject\yii\argumentresolver\resolvers\value\RequestAttributeValueResolver
 */
class RequestAttributeValueResolverTest extends TestCase
{
    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testSupportsParameter()
    {
        $resolver = new RequestAttributeValueResolver();
        $parameter = new ReflectionParameter(function ($param) {
        }, 'param');
        $requestParameters = [
            'param' => 213123,
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
        $resolver = new RequestAttributeValueResolver();
        $parameter = $parameter = new ReflectionParameter(function ($param) {
        }, 'param');
        $parameterScalar = new ReflectionParameter(function (string $param) {
        }, 'param');
        $parameterClass = new ReflectionParameter(function (ReflectionParameter $param) {
        }, 'param');
        $requestParameters = [
            'param' => [1231231],
        ];

        $this->assertFalse($resolver->supports($parameter, $requestParameters));
        $this->assertFalse($resolver->supports($parameterScalar, $requestParameters));
        $this->assertFalse($resolver->supports($parameterClass, $requestParameters));
    }
}
