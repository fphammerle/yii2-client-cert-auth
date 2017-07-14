<?php

namespace fphammerle\yii2\auth\clientcert\tests\migrations;

use fphammerle\yii2\auth\clientcert\tests\models\User;

class CreateUserTable extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable(
            User::tableName(),
            [
                'id' => $this->primaryKey(),
                'username' => $this->string(16)->notNull()->unique(),
                ]
            );
    }

    public function safeDown()
    {
        $this->dropTable(User::tableName());
    }
}
