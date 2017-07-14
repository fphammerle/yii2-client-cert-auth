<?php

namespace fphammerle\yii2\auth\clientcert;

class Subject extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'identity_cert_subject';
    }

    public function __construct($dn = null)
    {
        $this->distinguished_name = $dn;
    }

    public function rules()
    {
        return [
            [['distinguished_name'], 'required'],
            [['distinguished_name'], 'string'],
            [['distinguished_name'], 'unique'],
            ];
    }

    // public function getIdentityId()
    // {
    //     return $this->identity_id;
    // }

    // public static function getIdentityClass()
    // {
    //     return \Yii::$app->user->identityClass;
    // }
    //
    // public function getIdentity()
    // {
    //     return $this->hasOne(self::getIdentityClass(), ['id' => 'identity_id']);
    // }
}
