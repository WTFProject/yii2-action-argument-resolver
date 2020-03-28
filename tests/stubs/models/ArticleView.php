<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\stubs\models;

use yii\db\ActiveRecord;

/**
 * Class ArticleView
 * @package wtfproject\yii\argumentresolver\tests\stubs\models
 *
 * @property int $article_id
 * @property string $created_at
 *
 * @property-read \wtfproject\yii\argumentresolver\tests\stubs\models\Article $article
 */
class ArticleView extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName()
    {
        return 'article_view';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticle()
    {
        return $this->hasOne(Article::class, ['id' => 'article_id']);
    }
}
