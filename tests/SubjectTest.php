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
        (new Subject())->save();
        (new Subject())->save();
        (new Subject())->save();
        $subjects = ArrayHelper::map(
            Subject::find()->all(),
            function($s) { return $s->getAttributes(); }
        );
        $this->assertEquals(3, sizeof($subjects));
        $this->assertContains(['id' => 1], $subjects);
        $this->assertContains(['id' => 2], $subjects);
        $this->assertContains(['id' => 3], $subjects);
    }
}
