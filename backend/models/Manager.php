<?php
namespace backend\models;

use common\models\WxMember;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Manager model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $role
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class Manager extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE  = 10;
    const ROLE_USER      = 10;

    public $password;
    public $repassword;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%manager}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'mobile', 'realname', 'role', 'userid'], 'required'],
            [['password', 'repassword'], 'required', 'on' => 'create'],
            ['password', 'string', 'min' => 6],
            ['userid', 'string', 'max' => 64],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            ['repassword', 'compare', 'compareAttribute' => 'password', 'message' => '密码与确认密码必须一致'],
            ['mobile', 'mobileCheck'],
            ['email', 'email'],
            ['username', 'unique'],
            ['branch', 'integer'],
        ];
    }

    /**
     * 手机号检查
     * @param type $attribute
     * @param type $params
     */
    public function mobileCheck($attribute, $params)
    {
        if (!preg_match('/^(1)\d{10}$/', $this->mobile)) {
            $this->addError($attribute, '请输入正确的手机号');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username'   => '用户名',
            'email'      => '邮件地址',
            'role'       => '角色',
            'status'     => '状态',
            'mobile'     => '手机号',
            'branch'     => '分公司',
            'realname'   => '姓名',
            'password'   => '密码',
            'repassword' => '确认密码',
            'userid'     => '通讯录中的成员名',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @return \yii\db\ActiveQuery 与ScmStock重合
     */
    public function getWxMemberName()
    {
        return $this->hasOne(\common\models\WxMember::className(), ['userid' => 'userid']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status'               => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire    = Yii::$app->params['manager.passwordResetTokenExpire'];
        $parts     = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * 获取状态
     * @return string 状态
     */
    public function getStatus()
    {
        $statusArray = $this->getStatusArray();
        return $statusArray[$this->status];
    }

    /**
     * 获取商品状态数组
     * @return array 商品状态数组
     */
    public function getStatusArray()
    {
        return array(
            ''   => '请选择',
            '10' => '正常',
            '0'  => '禁止',
        );
    }

    /**
     * 获取状态
     * @return string 状态
     */
    public function getBranch()
    {
        $statusArray = \backend\models\Organization::getBranchArray();
        return $statusArray[$this->branch];
    }

    /**
     * 重设权限
     */
    public function resetAuth()
    {
        $auth = Yii::$app->authManager;
        $auth->revokeAll($this->id);
        $admin = $auth->getRole($this->role);
        $auth->assign($admin, $this->id);
    }

    /**
     * 获取指定角色的用户数
     * @param string $roleName
     * @return int 指定角色的用户数
     */
    public static function getUsers($roleName)
    {
        return self::find()->where(['role' => $roleName])->count();
    }

    /**
     * 获取机构ID
     * @return int 机构ID
     */
    public static function getManagerBranchID()
    {
        $manager = self::findOne(Yii::$app->user->id);
        return $manager->branch;
    }

    /**
     * 获取微信通信录中的用户id
     * @return [type] [description]
     */
    public static function getWxMemberId()
    {
        return self::findOne(Yii::$app->user->id)->userid;
    }

    /**
     * 根据通讯录中的用户id更新管理员所在分公司
     * @author  zgw
     * @version 2016-08-24
     * @param   integer     $branch 分公司id
     * @param   string      $userid 用户id
     * @return  boolean             更新结果
     */
    public static function changeBranch($branch, $userid)
    {
        return self::updateAll(['branch' => $branch], ['userid' => $userid]);
    }

    //通过后台管理员账号查到相对应的名称
    public static function getUserName($userName)
    {
        $managerModel = self::find()->where(['username' => $userName])->one();
        if (isset($managerModel->userid) && empty($managerModel->userid)) {
            return $userName = '管理员';
        }
        $userId = !empty($managerModel) ? $managerModel->userid : '';
        if ($userId == '') {
            return '暂无';
        }
        return WxMember::getMemberDetail("*", ['userid' => $userId])['name'];
    }

    /**
     *  查询出角色的一维数组
     *  用于管理员中的角色查询
     **/
    public static function getRoleArr()
    {
        $roleObjArr  = self::find()->asArray()->all();
        $roleNameArr = [];
        foreach ($roleObjArr as $key => $value) {
            $roleNameArr[$value['role']] = $value['role'];
        }
        return $roleNameArr;
    }

    /**
     * 获取指定的字段
     * @author  zgw
     * @version 2016-11-29
     * @param   [type]     $field [description]
     * @param   [type]     $where [description]
     * @return  [type]            [description]
     */
    public static function getField($field, $where)
    {
        $model = self::find()->select($field)->where($where)->one();
        return $model ? $model->$field : '';
    }

    /**
     * 禁用用户
     * @author wangxl
     * @param $userid
     * @return bool
     */
    public static function forbidManager($userid)
    {
        $manager = Manager::findOne(['userid' => $userid, 'status' => self::STATUS_ACTIVE]);
        if ($manager) {
            $manager->status = self::STATUS_DELETED;
            return $manager->save();
        }
        return true;
    }

    /**
     * 获取微信通信录中的用户名
     * @return [type] [description]
     */
    public static function getManagerRealname()
    {
        return self::findOne(Yii::$app->user->id)->realname;
    }
    /**
     * 根据用户ID 获取用户的
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-13
     * @param:    [str] 企业微信的用户ID
     * @return    [str]     [角色拥有的权限list]
     */
    public static function getManagerRoleList($userID)
    {
        $roleName = self::getManagerRole($userID);
        if ($roleName) {
            return AuthItemChild::getRoleNameList($roleName);
        } else {
            return [];
        }
    }
    /**
     *  根据用户ID 获取用户的
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-13
     * @param     [string]     $userID [企业微信的用户ID]
     * @return    [str]             [角色名称]
     */
    public static function getManagerRole($userID)
    {
        return self::find()
            ->select('role')
            ->where(['userid' => $userID])
            ->scalar();
    }
    /**
     * 获取微信端登录的用户分公司
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-16
     * @DateTime: 2018-11-13
     * @param     [string]     $userID [企业微信的用户ID]
     * @return    [str]             [分公司ID]
     */
    public static function getOrgIDByUser($userID)
    {
        return self::find()
            ->select('branch')
            ->where(['userid' => $userID])
            ->scalar();
    }
    /**
     * 获取登录的用户分公司ID和角色名称
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-16
     * @param     [string]     $userID [企业微信的用户ID]
     * @return    [array]             [角色名称和分公司ID]
     */
    public static function getManagerRoleAndOrg($userID)
    {
        return self::find()
            ->select('branch,role')
            ->where(['userid' => $userID])
            ->asArray()
            ->one();
    }
    /**
     * 获取所有的BD角色列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-06
     * @param     [int]     $orgID [分公司ID ]
     * @return    [array]          [BD角色列表]
     */
    public static function getManagerBdListByOrg($orgID)
    {
        return self::find()
            ->select('userid,username')
            ->where(['role' => 'BD'])
            ->andFilterWhere(['branch' => $orgID])
            ->asArray()
            ->all();
    }
    /**
     * 获取所有的提交审核人的角色列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-06
     * @param     [int]     $orgID [分公司ID ]
     * @return    [array]          [BD角色列表]
     */
    public static function getManagerApproverListByOrg($orgID)
    {
        return self::find()
            ->select('userid,username')
            ->where(['role' => ['BDM', 'BD', '区域运维主管', '区域零售', '总部零售', '总部零售总监']])
            ->andFilterWhere(['branch' => $orgID])
            ->asArray()
            ->all();
    }
}
