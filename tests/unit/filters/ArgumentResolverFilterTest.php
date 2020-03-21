<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\unit\filters;

use wtfproject\yii\argumentresolver\filters\ArgumentResolverFilter;
use wtfproject\yii\argumentresolver\tests\stubs\TestWebController;
use wtfproject\yii\argumentresolver\tests\unit\TestCase;
use Yii;

/**
 * Class ArgumentResolverFilterTest
 * @package wtfproject\yii\argumentresolver\tests\unit\filters\web
 *
 * @see \wtfproject\yii\argumentresolver\filters\ArgumentResolverFilter
 */
class ArgumentResolverFilterTest extends TestCase
{
    /**
     * {@inheritDoc}
     *
     * @throws \yii\base\Exception
     */
    public function setUp()
    {
        parent::setUp();

        $this->mockWebApplication();
    }

    public function testFilter()
    {
        Yii::$app->controller = new TestWebController('id', Yii::$app, [
            'as resolver' => [
                'class' => ArgumentResolverFilter::class,
                'configuration' => [

                ],
            ],
        ]);

        Yii::$app->controller->runAction('test', [
            'argInt' => '1',
            'argStr' => 'some string',
            'arg' => 'some arg',
            'argArr' => [1, '23123', 4],
        ]);

        $this->assertEquals(1, Yii::$app->controller->actionParams['argInt']);
        $this->assertInternalType('int', Yii::$app->controller->actionParams['argInt']);
    }
}
