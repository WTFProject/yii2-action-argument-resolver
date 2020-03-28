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
    public function testSupports()
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
    public function testDoesNotSupports()
    {
        $resolver = new RequestAttributeValueResolver();

        $parameter = new ReflectionParameter(function ($param) {
        }, 'param');
        $requestParameters = [];

        $this->assertFalse($resolver->supports($parameter, $requestParameters));
    }
}
