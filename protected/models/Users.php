<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property string $login
 * @property string $pass
 * @property string $mail
 */
class Users extends CActiveRecord
{
    public $rememberMe;

    private $_identity;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(

            // Your rules here
            // ...
            // ...
            
            // we set this rule only for registration, becouse during auth we will use mail and login as one field
            // you can set 'on' => 'reg, edit' if e-mail will be edited by user in their cabinet
            array('mail, email_repeat', 'email', 'on' => 'reg'), 


			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, login, pass, mail', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'login' => 'Login',
			'pass' => 'Password',
			'mail' => 'E-mail',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('login',$this->login,true);
		$criteria->compare('pass',$this->pass,true);
		$criteria->compare('mail',$this->mail,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Users the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * Before save new user
     * @return boolean
     */
    public function beforeSave()
    {
        if(parent::beforeSave())
        {
            // Your code here

            return true;
        }

        return false;
    }

    /**
     * Authenticate
     * @param type $attribute
     * @param type $params
     */
    public function authenticate($attribute, $params)
    {
        if(!$this->hasErrors())
        {
            $this->_identity = new UserIdentity(strtolower($this->mail), $this->pass);
            if(!$this->_identity->authenticate())
                $this->addError('pass', 'Incorrect e-mail (login) or password');
            else
                $this->login();
        }
    }

    /**
     * Login
     * @return boolean
     */
    public function login()
    {
        if($this->_identity === null)
        {
            $this->_identity = new UserIdentity(strtolower($this->mail), $this->pass);
            $this->_identity->authenticate();
        }
        if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
        {
            $duration=$this->rememberMe ? 3600*24*366 : 0; // Cookies autologin 366 days
            Yii::app()->user->login($this->_identity, $duration);
            return true;
        }
        else
            return false;
    }

}
