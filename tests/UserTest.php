<?php

namespace fphammerle\yii2\auth\clientcert\tests;

use \fphammerle\helpers\ArrayHelper;

class UserTest extends TestCase
{
    public function testCreateModel()
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

    public function testLoginLogout()
    {
        $app = $this->mockApplication();
        $this->assertNull($app->user->identity);
        $alice = new models\User('alice');
        $alice->save();
        $this->assertTrue($app->user->login($alice));
        $this->assertSame($alice, $app->user->identity);
        $app->user->logout();
        $this->assertNull($app->user->identity);
    }
}
