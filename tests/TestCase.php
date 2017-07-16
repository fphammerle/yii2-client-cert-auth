<?php

namespace fphammerle\yii2\auth\clientcert\tests;

use \fphammerle\yii2\auth\clientcert;

abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    public function mockApplication($app_config = [])
    {
        $app_config_default = [
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
        ];
        $app = new \yii\web\Application(
            array_replace_recursive($app_config_default, $app_config)
        );

        if(!isset($app_config['components']['db'])
            || !is_object($app_config['components']['db'])) {
            $this->assertEquals([], $app->db->getSchema()->getTableNames());
            ob_start();
            (new clientcert\tests\migrations\CreateUserTable)->up();
            ob_end_clean();
        }

        return $app;
    }

    public function createUser($username)
    {
        $u = new models\User;
        $u->username = $username;
        $this->assertTrue($u->save());
        return $u;
    }

    public function createSubject($user, $dn)
    {
        $subj = new clientcert\Subject;
        $subj->identity = $user;
        $subj->distinguished_name = $dn;
        $this->assertTrue($subj->save());
        return $subj;
    }

    public function createSubjectTable()
    {
        ob_start();
        (new clientcert\migrations\m170716_175707_create_identity_cert_subject_table)->up();
        ob_end_clean();
    }

    public function getIdentity()
    {
        return \Yii::$app->user->getIdentity();
    }
}
