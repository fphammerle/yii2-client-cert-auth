<?php

namespace fphammerle\yii2\auth\clientcert\tests;

use \fphammerle\helpers\ArrayHelper;
use \fphammerle\yii2\auth\clientcert\Subject;
use \fphammerle\yii2\auth\clientcert\migrations;

class SubjectTest extends TestCase
{
    protected $alice;
    protected $bob;

    protected function setUp()
    {
        $this->mockApplication();
        (new migrations\CreateSubjectTable)->up();

        $this->alice = new models\User('alice');
        $this->bob = new models\User('bob');
        $this->assertTrue($this->alice->save());
        $this->assertTrue($this->bob->save());
    }

    public function testCreateModel()
    {
        (new Subject($this->alice->id, 'CN=Alice,C=AT'))->save();
        (new Subject($this->alice->id, 'CN=Alice,O=Office,C=AT'))->save();
        (new Subject($this->bob->id, 'CN=Bob,C=AT'))->save();
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
        $this->assertTrue((new Subject($this->alice->id, 'CN=Alice,C=AT'))->save());
        $this->assertTrue((new Subject($this->bob->id, 'CN=Bob,C=AT'))->save());
        $dup = new Subject($this->alice->id, 'CN=Alice,C=AT');
        $this->assertFalse($dup->save());
        $this->assertEquals(1, sizeof($dup->getErrors()));
        $this->assertEquals(1, sizeof($dup->getErrors('distinguished_name')));
    }
}
