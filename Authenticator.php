<?php

namespace fphammerle\yii2\auth\clientcert;

class Authenticator extends \yii\base\Component
{
    use \fphammerle\helpers\PropertyAccessTrait;

    public function init()
    {
        parent::init();

        $this->loginByClientCertficiate();
    }

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

    /**
     * @return bool
     */
    public function getClientCertVerified()
    {
        return isset($_SERVER['SSL_CLIENT_VERIFY'])
            && $_SERVER['SSL_CLIENT_VERIFY'] == 'SUCCESS';
    }

    /**
     * @return IdentityInterface|null
     */
    public function loginByClientCertficiate()
    {
        if($this->getClientCertVerified()) {
            // Subject DN in client certificate
            return $this->loginByDistinguishedName($_SERVER["SSL_CLIENT_S_DN"]);
        } else {
            return null;
        }
    }
}
