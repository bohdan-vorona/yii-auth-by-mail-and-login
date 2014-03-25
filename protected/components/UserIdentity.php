<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
    /**
     * @var int current user id
     */
    private $_id;

    /**
     * Authenticates a user.
     * @return boolean whether authentication succeeds.
     */
    public function authenticate()
    {
        if (strpos($this->username, '@') == true) {
            $record = Users::model()->findByAttributes(array('mail'=>$this->username));
        } else {
            $record = Users::model()->findByAttributes(array('login'=>$this->username));
        }
        if($record === null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        }else if($record->pass !== md5($this->password) ) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        }
        else
        {
            // Yii::app()->user parameters
            $this->_id = $record->id;
            $this->setState('login', $record->login);
            $this->setState('mail', $record->mail);

            $this->errorCode = self::ERROR_NONE;
        }
        return !$this->errorCode;
    }

    /**
     * Get current user id
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }
}
