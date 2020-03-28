<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\stubs\controllers;

use wtfproject\yii\argumentresolver\tests\stubs\models\Article;
use yii\web\Controller;
use yii\web\Request;
use yii\web\View;

/**
 * Class TestWebController
 * @package wtfproject\yii\argumentresolver\tests\stubs\controllers
 */
class TestWebController extends Controller
{
    public function actionTestScalars(
        int $int, float $float, string $str, $strDefault = 'default', int $intNull = null
    ): string {
        return '';
    }

    public function actionTestObjects(Request $request, View $view = null): string
    {
        return '';
    }

    public function actionTestMixed(bool $bool, array $arr, Article $model, $param): string
    {
        return '';
    }

    public function actionTestVariadic(...$variadic): string
    {
        return '';
    }

    public function actionTestObject(View $view): string
    {
        return '';
    }
}
