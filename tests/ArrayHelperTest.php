<?php

namespace fphammerle\yii2\auth\clientcert\tests;

use \fphammerle\helpers\ArrayHelper;
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
        $this->assertEquals([], $app->db->getSchema()->getTableNames());
        (new migrations\CreateUserTable)->up();
        $this->assertNull($app->user->getIdentity());
        return $app;
    }

    public function testCreateUser()
    {
        $app = $this->mockApplication();
        (new models\User('a'))->save();
        (new models\User('b'))->save();
        $users = ArrayHelper::map(
            models\User::find()->all(),
            function($u) { return $u->getAttributes(); }
        );
        $this->assertEquals(2, sizeof($users));
        $this->assertContains(['id' => 1, 'username' => 'a'], $users);
        $this->assertContains(['id' => 2, 'username' => 'b'], $users);
    }
}
