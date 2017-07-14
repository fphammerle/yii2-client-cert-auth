<?php

namespace fphammerle\yii2\auth\clientcert\migrations;

use fphammerle\yii2\auth\clientcert\Subject;

class CreateSubjectTable extends \yii\db\Migration
{
    public function safeUp()
    {
        $identity_id_schema = Subject::getIdentityIdSchema();
        $identity_id_builder = $this->db->schema->createColumnSchemaBuilder(
            $identity_id_schema->dbType,
            $identity_id_schema->size
        );
        $this->createTable(Subject::tableName(), [
            'id' => $this->primaryKey(),
            'identity_id' => $identity_id_builder->notNull(),
            'distinguished_name' => $this->string()->notNull()->unique(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable(Subject::tableName());
    }
}
