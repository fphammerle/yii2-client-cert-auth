<?php

namespace fphammerle\yii2\auth\clientcert\tests;

use \fphammerle\helpers\ArrayHelper;
use \fphammerle\yii2\auth\clientcert\Subject;
use \fphammerle\yii2\auth\clientcert\migrations;

class DummyUser implements \yii\web\IdentityInterface
{
    public static function findIdentity($id) {}
    public static function findIdentityByAccessToken($token, $type = null) {}
    public function getId() {}
    public function getAuthKey() {}
    public function validateAuthKey($authKey) {}
}

class SubjectTest extends TestCase
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
    }

    public function testCreateModel()
    {
        (new Subject($this->alice, 'CN=Alice,C=AT'))->save();
        (new Subject($this->alice, 'CN=Alice,O=Office,C=AT'))->save();
        (new Subject($this->bob, 'CN=Bob,C=AT'))->save();
        $subjects = ArrayHelper::map(
            Subject::find()->all(),
            function($s) { return $s->getAttributes(); }
        );
        $this->assertEquals(3, sizeof($subjects));
        ArrayHelper::map(
            [['id' => 1, 'identity_id' => $this->alice->id, 'distinguished_name' => 'CN=Alice,C=AT'],
             ['id' => 2, 'identity_id' => $this->alice->id, 'distinguished_name' => 'CN=Alice,O=Office,C=AT'],
             ['id' => 3, 'identity_id' => $this->bob->id, 'distinguished_name' => 'CN=Bob,C=AT']],
            function($a) use ($subjects) {
                $this->assertContains($a, $subjects);
            }
        );
    }

    public function testDNUnique()
    {
        $this->assertTrue((new Subject($this->alice, 'CN=Alice,C=AT'))->save());
        $this->assertTrue((new Subject($this->bob, 'CN=Bob,C=AT'))->save());
        $dup = new Subject($this->alice, 'CN=Alice,C=AT');
        $this->assertFalse($dup->save());
        $this->assertEquals(1, sizeof($dup->getErrors()));
        $this->assertEquals(1, sizeof($dup->getErrors('distinguished_name')));
    }

    public function testGetIdentity()
    {
        $s = new Subject;
        $this->assertNull($s->identity);

        $s = new Subject($this->alice, 'CN=Alice,C=AT');
        $this->assertEquals($this->alice->id, $s->identity_id);
        $this->assertInstanceOf(models\User::className(), $s->identity);
        $this->assertEquals($this->alice->id, $s->identity->id);

        $s->identity_id = $this->bob->id;
        $s->save();
        $s = Subject::findOne(['identity_id' => $this->bob->id]);
        $this->assertEquals($this->bob->id, $s->identity_id);
        $this->assertInstanceOf(models\User::className(), $s->identity);
        $this->assertEquals($this->bob->id, $s->identity->id);
    }

    public function testSetIdentity()
    {
        $s = new Subject($this->alice, 'CN=Alice,C=AT');
        $this->assertInstanceOf(models\User::className(), $s->identity);
        $this->assertEquals($this->alice->id, $s->identity->id);

        $s->identity = $this->bob;
        $this->assertEquals($this->bob->id, $s->identity_id);
        // TODO: $this->assertEquals($this->bob->id, $s->identity->id);

        $s->save();
        $s = Subject::findOne(['identity_id' => $this->bob->id]);
        $this->assertEquals($this->bob->id, $s->identity_id);
        $this->assertEquals($this->bob->id, $s->identity->id);

        $s->identity = null;
        $this->assertNull($s->identity_id);
    }

    public function testSetIdentityInvalidType()
    {
        $s = new Subject;
        $this->setExpectedException('\InvalidArgumentException');
        $s->identity = new DummyUser;
    }

    public function testConstructInvalidType()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new Subject(new DummyUser);
    }

    public function testFindByDN()
    {
        $a1 = new Subject($this->alice, 'CN=Alice,C=AT');
        $a2 = new Subject($this->alice, 'CN=Alice,O=Office,C=AT');
        $b = new Subject($this->bob, 'CN=Bob,C=AT');
        $a1->save();
        $a2->save();
        $b->save();

        $this->assertEquals(
            $a1->id,
            Subject::findByDistinguishedName('CN=Alice,C=AT')->id
        );
        $this->assertEquals(
            $a2->id,
            Subject::findByDistinguishedName('CN=Alice,O=Office,C=AT')->id
        );
        $this->assertNull(
            Subject::findByDistinguishedName('CN=Bob,O=Office,C=AT')
        );
    }
}
