<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\stubs\models;

use yii\db\ActiveRecord;

/**
 * Class UserFavoriteArticle
 * @package wtfproject\yii\argumentresolver\tests\stubs\models
 *
 * @property string $user
 * @property int $article_id
 *
 * @property-read \wtfproject\yii\argumentresolver\tests\stubs\models\Article $article
 */
class UserFavoriteArticle extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName()
    {
        return 'user_favorite_article';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticle()
    {
        return $this->hasOne(Article::class, ['id' => 'article_id']);
    }
}
