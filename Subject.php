<?php

namespace fphammerle\yii2\auth\clientcert;

class Subject extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'identity_cert_subject';
    }

    public function __construct($identity_id = null, $dn = null)
    {
        $this->identity_id = $identity_id;
        $this->distinguished_name = $dn;
    }

    public function rules()
    {
        return [
            [['distinguished_name', 'identity_id'], 'required'],
            [['distinguished_name'], 'required'],
            [['distinguished_name'], 'string'],
            [['distinguished_name'], 'unique'],
            [['identity_id'], self::getIdentityIdSchema()->type],
            ];
    }

    public static function getIdentityClass()
    {
        return \Yii::$app->user->identityClass;
    }

    /**
     * @return \yii\db\TableSchema
     */
    public static function getIdentityTableSchema()
    {
        $cls = Subject::getIdentityClass();
        return (new $cls)->getTableSchema();
    }

    /**
     * @return \yii\db\ColumnSchema
     */
    public static function getIdentityIdSchema()
    {
        $keys = array_filter(
            self::getIdentityTableSchema()->columns,
            function($c) { return $c->isPrimaryKey; }
        );
        assert(sizeof($keys) == 1);
        return array_pop($keys);
    }

    // public function getIdentity()
    // {
    //     return $this->hasOne(self::getIdentityClass(), ['id' => 'identity_id']);
    // }
}
