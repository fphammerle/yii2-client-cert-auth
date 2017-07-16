<?php

namespace fphammerle\yii2\auth\clientcert\tests\integration;

use \fphammerle\yii2\auth\clientcert\Authenticator;
use \fphammerle\yii2\auth\clientcert\tests\TestCase;

class AuthenticatorTest extends TestCase
{
    protected $alice;
    protected $bob;

    protected function setUp()
    {
        $this->mockApplication();

        $this->createSubjectTable();

        $this->alice = $this->createUser('alice');
        $this->bob = $this->createUser('bob');

        $this->createSubject($this->alice, 'CN=Alice,C=AT');
        $this->createSubject($this->alice, 'CN=Alice,O=Office,C=AT');
        $this->createSubject($this->bob, 'CN=Bob,C=AT');
    }

    /**
     * @dataProvider loginByClientCertProvider
     */
    public function testLoginByClientCert($request_params, $username)
    {
        $_SERVER = array_replace_recursive($_SERVER, $request_params);

        $app = $this->mockApplication([
            'bootstrap' => ['clientCertAuth'],
            'components' => [
                'db' => \Yii::$app->db,
                'clientCertAuth' => Authenticator::className(),
            ],
        ]);

        if($username) {
            $this->assertEquals($username, $this->getIdentity()->username);
        } else {
            $this->assertNull($this->getIdentity());
        }
    }

    public function loginByClientCertProvider()
    {
        return [
            [['SSL_CLIENT_VERIFY' => null, 'SSL_CLIENT_S_DN' => null], null],
            [['SSL_CLIENT_VERIFY' => null, 'SSL_CLIENT_S_DN' => 'CN=Alice,C=AT'], null],
            [['SSL_CLIENT_VERIFY' => 'FAILED', 'SSL_CLIENT_S_DN' => 'CN=Alice,C=AT'], null],
            [['SSL_CLIENT_VERIFY' => 'NONE', 'SSL_CLIENT_S_DN' => 'CN=Alice,C=AT'], null],
            [['SSL_CLIENT_VERIFY' => 'SUCCESS', 'SSL_CLIENT_S_DN' => null], null],
            [['SSL_CLIENT_VERIFY' => 'SUCCESS', 'SSL_CLIENT_S_DN' => ''], null],
            [['SSL_CLIENT_VERIFY' => 'SUCCESS', 'SSL_CLIENT_S_DN' => 'CN=Alice,C=AT'], 'alice'],
            [['SSL_CLIENT_VERIFY' => 'SUCCESS', 'SSL_CLIENT_S_DN' => 'CN=Alice,O=Office,C=AT'], 'alice'],
            [['SSL_CLIENT_VERIFY' => 'SUCCESS', 'SSL_CLIENT_S_DN' => 'CN=Bob,C=AT'], 'bob'],
        ];
    }
}
