<?php

namespace fphammerle\yii2\auth\clientcert\tests;

use \fphammerle\helpers\ArrayHelper;
use \fphammerle\yii2\auth\clientcert\Subject;
use \fphammerle\yii2\auth\clientcert\migrations;

class SubjectTest extends TestCase
{
    public function testCreateModel()
    {
        $app = $this->mockApplication();
        (new migrations\CreateSubjectTable)->up();
        (new Subject('CN=Alice,C=AT'))->save();
        (new Subject('CN=Alice,O=Office,C=AT'))->save();
        (new Subject('CN=Bob,C=AT'))->save();
        $subjects = ArrayHelper::map(
            Subject::find()->all(),
            function($s) { return $s->getAttributes(); }
        );
        $this->assertEquals(3, sizeof($subjects));
        ArrayHelper::map(
            [['id' => 1, 'distinguished_name' => 'CN=Alice,C=AT'],
             ['id' => 2, 'distinguished_name' => 'CN=Alice,O=Office,C=AT'],
             ['id' => 3, 'distinguished_name' => 'CN=Bob,C=AT']],
            function($a) use ($subjects) {
                $this->assertContains($a, $subjects);
            }
        );
    }

    public function testDNUnique()
    {
        $app = $this->mockApplication();
        (new migrations\CreateSubjectTable)->up();
        $this->assertTrue((new Subject('CN=Alice,C=AT'))->save());
        $this->assertTrue((new Subject('CN=Bob,C=AT'))->save());
        $dup = new Subject('CN=Alice,C=AT');
        $this->assertFalse($dup->save());
        $this->assertEquals(1, sizeof($dup->getErrors()));
        $this->assertEquals(1, sizeof($dup->getErrors('distinguished_name')));
    }
}
