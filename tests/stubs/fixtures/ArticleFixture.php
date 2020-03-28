<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\stubs\fixtures;

use wtfproject\yii\argumentresolver\tests\stubs\models\Article;
use yii\test\ActiveFixture;

/**
 * Class ArticleFixture
 * @package wtfproject\yii\argumentresolver\tests\stubs\fixtures
 */
class ArticleFixture extends ActiveFixture
{
    /**
     * @var string
     */
    public $modelClass = Article::class;

    /**
     * {@inheritDoc}
     */
    protected function getData()
    {
        return [
            [
                'id' => 1,
                'title' => 'Lorem ipsum',
                'slug' => 'lorem-ipsum',
                'author' => 'John Doe',
                'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut ac felis tempor, molestie est et, viverra nisi. Sed id commodo quam, at luctus nunc. Aenean pharetra mauris id felis dapibus gravida. Mauris vitae purus sagittis, dapibus purus accumsan, ornare eros. Etiam ullamcorper urna nec massa consequat, a consectetur tellus pretium. Phasellus non placerat lorem. Cras quis fringilla quam.',
                'published_at' => '2020-03-28 12:00:00',
            ],
            [
                'id' => 2,
                'title' => 'Nam vitae nisl elit',
                'slug' => 'nam-vitae-nisl-elit',
                'author' => 'Arthur Doe',
                'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam vitae nisl elit. Pellentesque ut malesuada quam. Etiam ultricies metus sit amet purus interdum, sit amet faucibus magna tincidunt. Suspendisse mattis ante lacus, a condimentum nibh suscipit at. Etiam iaculis enim sit amet felis pulvinar sollicitudin. Sed viverra eget mauris eu ultricies. Nunc vitae convallis massa. Integer quis fermentum felis, at auctor elit. Fusce sollicitudin enim accumsan, dictum eros ut, dignissim massa. Nam commodo consequat quam nec dignissim. Ut vel massa nibh. Sed hendrerit euismod felis in porta.',
                'published_at' => '2020-03-28 13:00:00',
            ]
        ];
    }
}
