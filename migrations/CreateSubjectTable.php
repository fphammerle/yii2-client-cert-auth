<?php

namespace fphammerle\yii2\auth\clientcert\migrations;

use fphammerle\yii2\auth\clientcert\Subject;

class CreateSubjectTable extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable(Subject::tableName(), [
            'id' => $this->primaryKey(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable(Subject::tableName());
    }
}
