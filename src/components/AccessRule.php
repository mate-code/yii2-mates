<?php


namespace common\components;

use common\models\User;
use yii\web\IdentityInterface;

class AccessRule extends \yii\filters\AccessRule
{
    /**
     * @inheritdoc
     */
    protected function matchRole($user)
    {
        if (empty($this->roles)) {
            return true;
        }
        /** @var IdentityInterface $userModel */
        $userModel = $user->identity;
        $userRole = $userModel ? $userModel->userRole : null;

        foreach ($this->roles as $role) {
            if ($role === '?') {
                if ($user->getIsGuest()) {
                    return true;
                }
            } elseif ($role === '@') {
                if (!$user->getIsGuest()) {
                    return true;
                }
                // Check if the user is logged in, and the roles match
            } elseif (!$user->getIsGuest() && $role === $userRole->name) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param bool $beStrict
     * @return IdentityInterface
     */
    public static function activeUser($beStrict = true)
    {
        $activeUser = \Yii::$app->user->identity;
        if ($beStrict && !$activeUser) {
            throw new \RuntimeException("User not logged in");
        }
        return $activeUser;
    }
}