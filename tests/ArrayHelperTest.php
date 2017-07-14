<?php

namespace fphammerle\yii2\auth\clientcert\tests;

use \fphammerle\yii2\auth\clientcert\Authenticator;

class ClientCertAuthTest extends \PHPUnit_Framework_TestCase
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
        (new migrations\CreateUserTable)->up();
        return $app;
    }

    public function testDb()
    {
        $app = $this->mockApplication();
        var_dump($app->db->createCommand('SELECT * FROM user')->queryAll());
        $this->assertEquals('bar', Authenticator::foo());
        $this->assertNull($app->user->getIdentity());
    }
}
