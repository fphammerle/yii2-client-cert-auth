<?php

namespace fphammerle\yii2\auth\clientcert\migrations;

use fphammerle\yii2\auth\clientcert\Subject;

class m170716_175707_create_identity_cert_subject_table extends \yii\db\Migration
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

        if($this->db->driverName != 'sqlite') {
            $this->addForeignKey(
                Subject::tableName() . '_identity',
                Subject::tableName(),
                'identity_id',
                Subject::getIdentityTableSchema()->name,
                $identity_id_schema->name,
                'cascade',
                'restrict'
            );
        }
    }

    public function safeDown()
    {
        $this->dropTable(Subject::tableName());
    }
}
