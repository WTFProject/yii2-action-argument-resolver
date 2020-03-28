<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\stubs\models;

use yii\db\ActiveRecord;

/**
 * Class Article
 * @package wtfproject\yii\argumentresolver\tests\stubs\models
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $author
 * @property string $text
 * @property string $published_at
 *
 * @property-read \wtfproject\yii\argumentresolver\tests\stubs\models\ArticleView[] $views
 */
class Article extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getViews()
    {
        return $this->hasMany(ArticleView::class, ['article_id' => 'id']);
    }
}
