<?php

namespace fphammerle\yii2\auth\clientcert\tests;

use \fphammerle\yii2\auth\clientcert\Authenticator;
use \fphammerle\yii2\auth\clientcert\Subject;
use \fphammerle\yii2\auth\clientcert\migrations;

class AuthenticatorTest extends TestCase
{
    protected $alice;
    protected $bob;

    protected function setUp()
    {
        $this->mockApplication();
        ob_start();
        (new migrations\CreateSubjectTable)->up();
        ob_end_clean();

        $this->alice = new models\User('alice');
        $this->bob = new models\User('bob');
        $this->assertTrue($this->alice->save());
        $this->assertTrue($this->bob->save());

        (new Subject($this->alice, 'CN=Alice,C=AT'))->save();
        (new Subject($this->alice, 'CN=Alice,O=Office,C=AT'))->save();
        (new Subject($this->bob, 'CN=Bob,C=AT'))->save();

        $this->assertNull(self::getIdentity());
    }

    public static function getIdentity()
    {
        return \Yii::$app->user->getIdentity();
    }

    public function testLoginByDN()
    {
        $a = new Authenticator;
        $this->assertNull(self::getIdentity());

        $u = $a->loginByDistinguishedName('CN=Alice,C=AT');
        $this->assertEquals($this->alice->id, $u->id);
        $this->assertEquals($this->alice->id, self::getIdentity()->id);

        $u = $a->loginByDistinguishedName('CN=Alice,O=Secret,C=AT');
        $this->assertNull($u);
        $this->assertEquals($this->alice->id, self::getIdentity()->id);

        $u = $a->loginByDistinguishedName('CN=Bob,C=AT');
        $this->assertEquals($this->bob->id, $u->id);
        $this->assertEquals($this->bob->id, self::getIdentity()->id);

        $u = $a->loginByDistinguishedName('');
        $this->assertNull($u);
        $this->assertEquals($this->bob->id, self::getIdentity()->id);

        $u = $a->loginByDistinguishedName(NULL);
        $this->assertNull($u);
        $this->assertEquals($this->bob->id, self::getIdentity()->id);
    }

    /**
     * @dataProvider loginByClientCertProvider
     */
    public function testLoginByClientCert($request_params, $username)
    {
        $_SERVER = $request_params;

        $a = new Authenticator;
        $this->assertNull(self::getIdentity());

        $u = $a->loginByClientCertficiate();

        if($username) {
            $this->assertEquals($username, self::getIdentity()->username);
            $this->assertEquals($username, $u->username);
        } else {
            $this->assertNull($u);
            $this->assertNull(self::getIdentity());
        }
    }

    public function loginByClientCertProvider()
    {
        return [
            [[], null],
            [['SSL_CLIENT_S_DN' => 'CN=Alice,C=AT'], null],
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
