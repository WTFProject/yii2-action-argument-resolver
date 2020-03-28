<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\unit\resolvers\value;

use ReflectionParameter;
use wtfproject\yii\argumentresolver\config\ActiveRecordConfiguration as Configuration;
use wtfproject\yii\argumentresolver\resolvers\value\ActiveRecordValueResolver;
use wtfproject\yii\argumentresolver\tests\stubs\fixtures\ArticleFixture;
use wtfproject\yii\argumentresolver\tests\stubs\models\Article;
use wtfproject\yii\argumentresolver\tests\stubs\models\ArticleView;
use wtfproject\yii\argumentresolver\tests\stubs\models\UserFavoriteArticle;
use wtfproject\yii\argumentresolver\tests\unit\TestCase;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class ActiveRecordValueResolverTest
 * @package wtfproject\yii\argumentresolver\tests\unit\resolvers\value
 *
 * @see \wtfproject\yii\argumentresolver\resolvers\value\ActiveRecordValueResolver
 */
class ActiveRecordValueResolverTest extends TestCase
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
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function testSupports()
    {
        $resolver = new ActiveRecordValueResolver();

        $parameter = new ReflectionParameter(function (Article $model) {
        }, 'model');
        $requestParams = [];
        $configuration = new Configuration();

        $this->assertTrue($resolver->supports($parameter, $requestParams));
        $this->assertTrue($resolver->supports($parameter, $requestParams, $configuration));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function testDoesNotSupports()
    {
        $resolver = new ActiveRecordValueResolver();

        $parameter = new ReflectionParameter(function ($model) {
        }, 'model');
        $requestParams = [];
        $configuration = new Configuration();

        $this->assertFalse($resolver->supports($parameter, $requestParams));
        $this->assertFalse($resolver->supports($parameter, $requestParams, $configuration));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function testSuccessResolveRequestAttribute()
    {
        $resolver = new ActiveRecordValueResolver();
        $configuration = new Configuration();

        $this->assertTrue(Yii::$app->has('db'));

        $result = $this->invoke($resolver, 'resolveAttribute', [$configuration, Article::class]);

        $this->assertEquals('id', $result);

        $configuration->attribute = 'slug';

        $result = $this->invoke($resolver, 'resolveAttribute', [$configuration, Article::class]);

        $this->assertEquals('slug', $result);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function testFailedResolveRequestAttribute()
    {
        $resolver = new ActiveRecordValueResolver();
        $configuration = new Configuration();

        $this->assertTrue(Yii::$app->has('db'));

        $this->expectException(NotSupportedException::class);
        $this->expectExceptionMessage('ActiveRecord model without primary key is not supported.');

        $this->invoke($resolver, 'resolveAttribute', [$configuration, ArticleView::class]);

        $this->expectException(NotSupportedException::class);
        $this->expectExceptionMessage('Composite primary key is not supported for ActiveRecord resolver.');

        $this->invoke($resolver, 'resolveAttribute', [$configuration, UserFavoriteArticle::class]);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\NotSupportedException
     * @throws \yii\web\NotFoundHttpException
     */
    public function testResolveOnMissingRequestAttribute()
    {
        $resolver = new ActiveRecordValueResolver();

        $parameter = new ReflectionParameter(function (Article $model) {
        }, 'model');
        $parameterNullable = new ReflectionParameter(function (Article $model = null) {
        }, 'model');
        $requestParams = [];

        $this->assertTrue(Yii::$app->has('db'));

        $this->expectException(NotFoundHttpException::class);

        $resolver->resolve($parameter, $requestParams);

        $this->assertNull($resolver->resolve($parameterNullable, $requestParams));
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\NotSupportedException
     * @throws \yii\web\NotFoundHttpException
     */
    public function testSuccessResolve()
    {
        $this->initFixtures();

        $resolver = new ActiveRecordValueResolver();

        $parameter = new ReflectionParameter(function (Article $model) {
        }, 'model');
        $requestParams = [
            'id' => 1,
            'slug' => 'lorem-ipsum',
        ];
        $configuration = new Configuration(['attribute' => 'slug']);

        $this->assertTrue(Yii::$app->has('db'));

        $model = $resolver->resolve($parameter, $requestParams);

        $this->assertInstanceOf(Article::class, $model);
        $this->assertEquals(1, $model->id);

        $model = $resolver->resolve($parameter, $requestParams, $configuration);

        $this->assertInstanceOf(Article::class, $model);
        $this->assertEquals(1, $model->id);

        $requestParams['slug'] = 'nam-vitae-nisl-elit';
        $configuration->findCallback = function ($slug) {
            $this->assertEquals('nam-vitae-nisl-elit', $slug);

            return Article::findOne(['slug' => $slug]);
        };

        $model = $resolver->resolve($parameter, $requestParams, $configuration);

        $this->assertInstanceOf(Article::class, $model);
        $this->assertEquals(2, $model->id);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\NotSupportedException
     * @throws \yii\web\NotFoundHttpException
     */
    public function testCustomNotFoundCallback()
    {
        $resolver = new ActiveRecordValueResolver();

        $parameter = new ReflectionParameter(function (Article $model) {
        }, 'model');
        $requestParams = [];
        $configuration = new Configuration(['notFoundCallback' => function () {
            throw new BadRequestHttpException();
        }]);

        $this->assertTrue(Yii::$app->has('db'));

        $this->expectException(BadRequestHttpException::class);

        $resolver->resolve($parameter, $requestParams, $configuration);
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\NotSupportedException
     * @throws \yii\web\NotFoundHttpException
     */
    public function testFindCallbackInvalidReturnType()
    {
        $resolver = new ActiveRecordValueResolver();

        $parameter = new ReflectionParameter(function (Article $model) {
        }, 'model');
        $requestParams = [
            'id' => 1,
        ];
        $configuration = new Configuration(['findCallback' => function ($id) {
            return 123;
        }]);

        $this->assertTrue(Yii::$app->has('db'));

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage(
            \sprintf('Invalid data type: "integer". "%s" is expected.', Article::class)
        );

        $resolver->resolve($parameter, $requestParams, $configuration);
    }
}
