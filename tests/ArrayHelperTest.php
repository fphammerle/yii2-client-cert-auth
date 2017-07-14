<?php

use fphammerle\yii2\ClientCertAuth;

class ClientCertAuthTest extends \PHPUnit_Framework_TestCase
{
    public function mockApplication()
    {
        return new \yii\web\Application([
            'id' => 'yii2-client-cert-auth-test',
            'basePath' => __DIR__,
            // 'vendorPath' => dirname(__DIR__) . '/vendor',
            'components' => [
                'db' => [
                    'class' => '\yii\db\Connection',
                    'dsn' => 'sqlite::memory:',
                ],
            ],
        ]);
    }

    public function testDb()
    {
        $app = $this->mockApplication();
        $app->db->createCommand('CREATE TABLE test ( x INT )')->execute();
        $app->db->createCommand('INSERT INTO test (x) VALUES (1), (2), (4)')->execute();
        var_dump($app->db->createCommand('SELECT * FROM test')->queryAll());
        $this->assertEquals('bar', ClientCertAuth::foo());
    }
}
