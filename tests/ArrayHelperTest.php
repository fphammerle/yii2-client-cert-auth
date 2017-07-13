<?php

use fphammerle\yii2\ClientCertAuth;

class ClientCertAuthTest extends \PHPUnit_Framework_TestCase
{
    public function testFlattenEmpty()
    {
        $this->assertEquals('bar', ClientCertAuth::foo());
    }
}
