<?php

namespace fphammerle\yii2\auth\clientcert\tests;

use \fphammerle\yii2\auth\clientcert\Authenticator;

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

        $this->assertNull($this->getIdentity());
    }

    public function testLoginByDN()
    {
        $a = new Authenticator;
        $this->assertNull($this->getIdentity());

        $u = $a->loginByDistinguishedName('CN=Alice,C=AT');
        $this->assertEquals($this->alice->id, $u->id);
        $this->assertEquals($this->alice->id, $this->getIdentity()->id);

        $u = $a->loginByDistinguishedName('CN=Alice,O=Secret,C=AT');
        $this->assertNull($u);
        $this->assertEquals($this->alice->id, $this->getIdentity()->id);

        $u = $a->loginByDistinguishedName('CN=Bob,C=AT');
        $this->assertEquals($this->bob->id, $u->id);
        $this->assertEquals($this->bob->id, $this->getIdentity()->id);

        $u = $a->loginByDistinguishedName('');
        $this->assertNull($u);
        $this->assertEquals($this->bob->id, $this->getIdentity()->id);

        $u = $a->loginByDistinguishedName(NULL);
        $this->assertNull($u);
        $this->assertEquals($this->bob->id, $this->getIdentity()->id);
    }

    /**
     * @dataProvider loginByClientCertProvider
     */
    public function testLoginByClientCert($request_params, $username)
    {
        $_SERVER = $request_params;

        $a = new Authenticator;
        $this->assertNull($this->getIdentity());

        $u = $a->loginByClientCertficiate();

        if($username) {
            $this->assertEquals($username, $this->getIdentity()->username);
            $this->assertEquals($username, $u->username);
        } else {
            $this->assertNull($u);
            $this->assertNull($this->getIdentity());
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
