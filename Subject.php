<?php

namespace fphammerle\yii2\auth\clientcert;

class Subject extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'identity_cert_subject';
    }

    public function __construct(\yii\web\IdentityInterface $identity = null, $dn = null)
    {
        $this->identity = $identity;
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

    public function getIdentity()
    {
        return $this->hasOne(
            self::getIdentityClass(),
            [Subject::getIdentityIdSchema()->name => 'identity_id']
        );
    }

    public function setIdentity(\yii\web\IdentityInterface $identity = null)
    {
        if($identity === null) {
            $this->identity_id = null;
        } else {
            $cls = self::getIdentityClass();
            if($identity instanceof $cls) {
                // @see \yii\web\IdentityInterface::getId()
                $this->identity_id = $identity->getId();
            } else {
                throw new \InvalidArgumentException(sprintf(
                    "expected instance of %s,\n%s given",
                    $cls,
                    get_class($identity)
                ));
            }
        }

        // TODO: update related record
        // $this->getRelatedRecords()['identity'] = $identity;
    }

    public function findByDistinguishedName($dn)
    {
        return self::findOne(['distinguished_name' => $dn]);
    }
}
