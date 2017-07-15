<?php

namespace fphammerle\yii2\auth\clientcert;

class Authenticator extends \yii\base\Component
{
    /**
     * @see \yii\web\User::switchIdentity
     * @return IdentityInterface|null
     */
    public function loginByDistinguishedName($dn, $duration = 0)
    {
        $subj = Subject::findByDistinguishedName($dn);
        if($subj) {
            \Yii::$app->user->switchIdentity($subj->identity, $duration);
            if(\Yii::$app->user->identity == $subj->identity) {
                return $subj->identity;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
}
