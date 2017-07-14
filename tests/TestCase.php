<?php

namespace fphammerle\yii2\auth\clientcert\tests;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    public function mockApplication()
    {
        $app = new \yii\web\Application([
            'id' => 'yii2-client-cert-auth-test',
            'basePath' => __DIR__,
            // 'vendorPath' => dirname(__DIR__) . '/vendor',
            'components' => [
                'db' => [
                    'class' => '\yii\db\Connection',
                    'dsn' => 'sqlite::memory:',
                ],
                'user' => [
                    'identityClass' => models\User::className(),
                ],
            ],
        ]);
        $this->assertEquals([], $app->db->getSchema()->getTableNames());
        ob_start();
        (new migrations\CreateUserTable)->up();
        ob_end_clean();
        $this->assertNull($app->user->getIdentity());
        return $app;
    }
}
