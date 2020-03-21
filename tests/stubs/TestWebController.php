<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\stubs;

use yii\web\Controller;

/**
 * Class TestWebController
 * @package wtfproject\yii\argumentresolver\tests\stubs
 */
class TestWebController extends Controller
{
    public function actionTest(int $argInt, string $argStr, $arg, array $argArr)
    {
        return '';
    }

    public function actionTestResolver(
        int $intArg,
        string $stringArg,
        float $floatArg,
        array $arrayArg,
        $simpleArg,
        int $intArgDefault = 26
    ) {
        return '';
    }
}
