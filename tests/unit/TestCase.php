<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\unit;

use ReflectionClass;
use Yii;
use yii\di\Container;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

/**
 * Class TestCase
 * @package wtfproject\yii\argumentresolver\tests\unit
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public static $params;

    /**
     * Clean up after test.
     * By default the application created with [[mockApplication]] will be destroyed.
     *
     * @return void
     *
     * @throws \yii\base\ErrorException
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->destroyApplication();
    }

    /**
     * Returns a test configuration param from /data/config.php
     *
     * @param string $name params name
     * @param mixed $default default value to use when param is not set.
     *
     * @return mixed  the value of the configuration param
     */
    public static function getParam(string $name, $default = null)
    {
        if (static::$params === null) {
//            static::$params = require(__DIR__ . '/data/config.php');
            static::$params = [];
        }

        return isset(static::$params[$name]) ? static::$params[$name] : $default;
    }

    /**
     * Populates Yii::$app with a new application
     * The application will be destroyed on tearDown() automatically.
     *
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     *
     * @return void
     *
     * @throws \yii\base\Exception
     */
    protected function mockApplication(array $config = [], string $appClass = '\yii\console\Application')
    {
        FileHelper::createDirectory(\dirname(__DIR__) . '/_output/proxy');

        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => \dirname(__DIR__) . '/vendor',
            'extensions' => [
                'wtfproject/yii2-action-argument-converter' => [
                    'name' => 'wtfproject/yii2-action-argument-converter',
                    'version' => '0.0.1',
                    'bootstrap' => [
                        'class' => '\wtfproject\yii\argumentresolver\Bootstrap',
                        'proxyCachePath' => \dirname(__DIR__) . '/_output/proxy',
                    ],
                ],
            ],
        ], $config));
    }

    /**
     * Populates Yii::$app with a new application
     * The application will be destroyed on tearDown() automatically.
     *
     * @param array $config
     * @param string $appClass
     *
     * @return void
     *
     * @throws \yii\base\Exception
     */
    protected function mockWebApplication(array $config = [], string $appClass = '\yii\web\Application')
    {
        FileHelper::createDirectory(\dirname(__DIR__) . '/_output/proxy');

        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => \dirname(__DIR__) . '/vendor',
            'components' => [
                'request' => [
                    'cookieValidationKey' => 'wefJDF8sfdsfSDefwqdxj9oq',//TODO:
                    'scriptFile' => __DIR__ . '/index.php',
                    'scriptUrl' => '/index.php',
                ],
            ],
            'extensions' => [
                'wtfproject/yii2-action-argument-converter' => [
                    'name' => 'wtfproject/yii2-action-argument-converter',
                    'version' => '0.0.1',
                    'bootstrap' => [
                        'class' => '\wtfproject\yii\argumentresolver\Bootstrap',
                        'proxyCachePath' => \dirname(__DIR__) . '/_output/proxy',
                    ],
                ],
            ],
        ], $config));
    }

    /**
     * Destroys application in Yii::$app by setting it to null.
     *
     * @return void
     *
     * @throws \yii\base\ErrorException
     */
    protected function destroyApplication()
    {
        Yii::$app = null;
        Yii::$container = new Container();

        FileHelper::removeDirectory(\dirname(__DIR__) . '/_output/proxy');
    }

    /**
     * Invokes object method, even if it is private or protected.
     *
     * @param object $object object.
     * @param string $method method name.
     * @param array $args method arguments
     *
     * @return mixed method result
     *
     * @throws \ReflectionException
     */
    protected function invoke($object, string $method, array $args = [])
    {
        $classReflection = new ReflectionClass(get_class($object));

        $methodReflection = $classReflection->getMethod($method);
        $methodReflection->setAccessible(true);

        $result = $methodReflection->invokeArgs($object, $args);

        $methodReflection->setAccessible(false);

        return $result;
    }
}
