<?php

namespace fphammerle\yii2\auth\clientcert\tests;

use \fphammerle\yii2\auth\clientcert\Subject;

class SubjectStaticTest extends TestCase
{
    protected function setUp()
    {
        $this->mockApplication();
    }
    public function testGetIdentityClass()
    {
        $this->assertEquals(
            'fphammerle\yii2\auth\clientcert\tests\models\User',
            Subject::getIdentityClass()
        );
    }

    public function testGetIdentityTableSchema()
    {
        $this->assertEquals(
            'user',
            Subject::getIdentityTableSchema()->name
        );
    }

    public function testGetIdentityIdSchema()
    {
        $schema = Subject::getIdentityIdSchema();
        $this->assertEquals('id', $schema->name);
        $this->assertEquals('integer', $schema->type);
        $this->assertTrue($schema->isPrimaryKey);
    }
}
