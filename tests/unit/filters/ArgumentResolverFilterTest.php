<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\unit\filters;

use wtfproject\yii\argumentresolver\config\ActiveRecordConfiguration;
use wtfproject\yii\argumentresolver\filters\ArgumentResolverFilter;
use wtfproject\yii\argumentresolver\tests\stubs\controllers\TestWebController;
use wtfproject\yii\argumentresolver\tests\stubs\fixtures\ArticleFixture;
use wtfproject\yii\argumentresolver\tests\stubs\models\Article;
use wtfproject\yii\argumentresolver\tests\unit\TestCase;
use Yii;
use yii\web\BadRequestHttpException;

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
     */
    public function fixtures()
    {
        return [
            ArticleFixture::class,
        ];
    }

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
     * @throws \yii\base\InvalidRouteException
     *
     * @see \wtfproject\yii\argumentresolver\tests\stubs\controllers\TestWebController::actionTestMixed()
     */
    public function testSuccessResolve()
    {
        $this->loadFixtures();

        $controller = new TestWebController('id', Yii::$app, [
            'as resolver' => [
                'class' => ArgumentResolverFilter::class,
                'configuration' => [
                    'test-mixed' => [
                        'model' => [
                            'class' => ActiveRecordConfiguration::class,
                            'attribute' => 'slug',
                        ],
                    ],
                ],
            ],
        ]);

        $controller->runAction('test-mixed', [
            'bool' => 'on',
            'arr' => ['23', 'str', '45'],
            'slug' => 'nam-vitae-nisl-elit',
            'param' => 'string',
        ]);

        $this->assertArrayHasKey('bool', $controller->actionParams);
        $this->assertEquals(true, $controller->actionParams['bool']);

        $this->assertArrayHasKey('arr', $controller->actionParams);
        $this->assertTrue(\is_array($controller->actionParams['arr']));

        $this->assertArrayHasKey('model', $controller->actionParams);
        $this->assertInstanceOf(Article::class, $controller->actionParams['model']);
        $this->assertEquals(2, $controller->actionParams['model']->id);

        $this->assertArrayHasKey('param', $controller->actionParams);
        $this->assertEquals('string', $controller->actionParams['param']);
    }

    /**
     * @return void
     *
     * @throws \yii\base\InvalidRouteException
     *
     * @see \wtfproject\yii\argumentresolver\tests\stubs\controllers\TestWebController::actionTestScalars()
     */
    public function testResolveFailedInvalidDataReceived()
    {
        $controller = new TestWebController('id', Yii::$app, [
            'as resolver' => [
                'class' => ArgumentResolverFilter::class,
            ],
        ]);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage(Yii::t('yii', 'Invalid data received for parameter "{param}".', [
            'param' => 'int',
        ]));

        $controller->runAction('test-scalars', ['int' => null]);
    }

    /**
     * @return void
     *
     * @throws \yii\base\InvalidRouteException
     *
     * @see \wtfproject\yii\argumentresolver\tests\stubs\controllers\TestWebController::actionTestScalars()
     */
    public function testResolveFailedMissingParams()
    {
        $controller = new TestWebController('id', Yii::$app, [
            'as resolver' => [
                'class' => ArgumentResolverFilter::class,
            ],
        ]);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage(Yii::t('yii', 'Missing required parameters: {params}', [
            'params' => \implode(', ', ['int', 'float', 'str']),
        ]));

        $controller->runAction('test-scalars', []);
    }
}
