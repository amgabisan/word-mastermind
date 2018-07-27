<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property string $email_address
 * @property string $first_name
 * @property string $last_name
 * @property string $status
 * @property string $ins_time
 * @property string $up_time
 */
class User extends ActiveRecord implements IdentityInterface
{
    public $confirmPassword;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password', 'email_address', 'first_name', 'last_name'], 'required'],
            [['status'], 'string'],
            [['ins_time', 'up_time'], 'safe'],
            [['username'], 'string', 'max' => 30],
            [['password'], 'string', 'max' => 255],
            [['email_address', 'first_name', 'last_name'], 'string', 'max' => 100],
            [['username', 'email_address'], 'unique'],
            [['password', 'confirmPassword'], 'required', 'on' => 'registerAccount'],
            ['confirmPassword', 'compare', 'compareAttribute' => 'password',
             'message' => Yii::t('app', "Passwords don't match.")],
            [['username', 'password', 'email_address', 'first_name', 'last_name', 'status'], 'trim'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'email_address' => 'Email Address',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'status' => 'Status',
            'ins_time' => 'Ins Time',
            'up_time' => 'Up Time',
        ];
    }

    /******************************** Identity Interface ********************************/
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }
    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return boolean if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /******************************** Custom Functions ********************************/

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
        validatePassword()
        @param  [string]    [password] - password inputted by the user
        @param  [string]    [hash_password] - hash password fetch from the database
    **/
    public static function validatePassword($password, $hash_password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $hash_password);
    }

    /**
    * This function saves data to user table.
    *
    *   @param    obj    $account
    */
    public function saveUser($account)
    {
        $model = new User;

        $model->username        = $account['username'];
        $model->password        = $model->confirmPassword =  Yii::$app->getSecurity()->generatePasswordHash($account['password']);
        $model->email_address   = $account['email_address'];
        $model->first_name      = $account['first_name'];
        $model->last_name       = $account['last_name'];
        $model->status          = 'active';
        $model->ins_time        = $model->up_time = Yii::$app->formatter->asDatetime('now');

        if ($model->save()) {
            return $model;
        }

        return false;
    }
}
