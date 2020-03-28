<?php

declare(strict_types=1);

namespace wtfproject\yii\argumentresolver\tests\unit;

use ReflectionClass;
use Yii;
use yii\base\Event;
use yii\di\Container;
use yii\helpers\ArrayHelper;
use yii\test\FixtureTrait;

/**
 * Class TestCase
 * @package wtfproject\yii\argumentresolver\tests\unit
 */
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    use FixtureTrait;

    /**
     * Clean up after test.
     * By default the application created with [[mockApplication]] will be destroyed.
     *
     * @return void
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->destroyApplication();
    }

    /**
     * Populates Yii::$app with a new application
     * The application will be destroyed on tearDown() automatically.
     *
     * @param array $config
     * @param string $appClass
     *
     * @return void
     */
    protected function mockWebApplication(array $config = [], string $appClass = '\yii\web\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => \dirname(__DIR__) . '/vendor',
            'components' => [
                'db' => [
                    'class' => '\yii\db\Connection',
                    'dsn' => 'sqlite:' . \dirname(__DIR__) . '/_output/data.db',
                    'on afterOpen' => function (Event $event) {
                        $event->sender
                            ->createCommand(\file_get_contents(\dirname(__DIR__) . '/_data/dump.sql'))
                            ->execute();
                    },
                ],
                'request' => [
                    'cookieValidationKey' => 'wefJDF8sfdsfSDefwqdxj9oq',
                    'scriptFile' => __DIR__ . '/index.php',
                    'scriptUrl' => '/index.php',
                ],
                'view' => [
                    'title' => 'app_view',
                ],
            ],
            'modules' => [
                'test' => [
                    'class' => '\yii\base\Module',
                    'components' => [
                        'view' => [
                            'class' => '\yii\web\View',
                            'title' => 'test_module_view',
                        ],
                    ],
                    'modules' => [
                        'test' => [
                            'class' => '\yii\base\Module',
                            'components' => [
                                'view' => [
                                    'class' => '\yii\web\View',
                                    'title' => 'test_embed_module_view',
                                ],
                            ],
                        ],
                    ],
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
     */
    protected function destroyApplication()
    {
        if (null !== $this->_fixtures) {
            $this->unloadFixtures();
        }

        Yii::$app = null;
        Yii::$container = new Container();
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
        $classReflection = new ReflectionClass(\get_class($object));

        $methodReflection = $classReflection->getMethod($method);
        $methodReflection->setAccessible(true);

        $result = $methodReflection->invokeArgs($object, $args);

        $methodReflection->setAccessible(false);

        return $result;
    }
}
