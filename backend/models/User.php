<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $role
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $openid
 * @property string $nickname
 * @property string $realname
 * @property integer $sex
 * @property string $province
 * @property string $mobile
 *
 * @property UserCoupon[] $userCoupons
 */
class User extends \yii\db\ActiveRecord
{
    
    
    /*
     * 注册起始日期
     */
    public $createFrom;

    /*
     * 注册截止日期
     */
    public $createTo;     
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'created_at', 'updated_at', 'mobile', 'is_master'], 'required'],
            [['role', 'status', 'created_at', 'updated_at', 'sex', 'equipment_id'], 'integer'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'openid'], 'string', 'max' => 255],
            [['auth_key', 'province'], 'string', 'max' => 32],
            [['nickname'], 'string', 'max' => 50],
            [['realname'], 'string', 'max' => 20],
            [['mobile'], 'string', 'max' => 15]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'role' => '用户角色',
            'status' => '用户状态',
            'created_at' => '注册时间',
            'updated_at' => '上次更新时间',
            'openid' => '微信OPENID',
            'nickname' => '昵称',
            'realname' => '姓名',
            'sex' => '姓别',
            'province' => '省份',
            'mobile' => '手机号',
            'interest_balance'=>'红利余额',
            'interest_total'=>'红利收入总计',
            'interest_draw'=>'红利提现总计',
            'createFrom' => '注册开始日期',
            'createTo' => '注册截止日期',
            'equipment_id'=>'注册楼宇'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserCoupons()
    {
        return $this->hasMany(UserCoupon::className(), ['user_id' => 'id']);
    }
    
    /**
     * 获取帐户状态
     * @return string 帐户状态
     */
    public function getStatus(){
        $statusArray = $this->getStatusArray();
        return $statusArray[$this->status];
    }
    
    public function getEquipment(){
        if(empty($this->equipment_id))
            return '';
        else{
            $equipment = \common\models\Equipments::findOne($this->equipment_id);
            return $equipment->building;
        }
    }
    
    /**
     * 获取帐户状态数组
     * @return array 帐户状态数组
     */
    public function getStatusArray(){
        return array(
            ''=>'请选择',
            '0'=>'禁用',
            '1'=>'正常'
        );
    }   
    
    /**
     * 获取角色
     * @return string 角色
     */
    public function getRole(){
        $roleArray = $this->getRoleArray();
        return $roleArray[$this->role];
    }
    
    /**
     * 获取角色数组
     * @return array 角色数组
     */
    public function getRoleArray(){
        return array(
            ''=>'请选择',
            '0'=>'普通用户',
            '1'=>'VIP用户',
        );
    }   
    
    /**
     * 获取性别
     * @return string 性别
     */
    public function getSex(){
        $sexArray = $this->getSexArray();
        return $sexArray[$this->sex];
    }
    
    /**
     * 获取性别数组
     * @return array 性别数组
     */
    public function getSexArray(){
        return array(
            '0'=>'女',
            '1'=>'男'
        );
    }    
    
    /**
     * 获取省
     * @return string 省
     */
    public function getProvince(){
        $regionArray = Region::getChild(Region::CHINA);
        return $regionArray[$this->province];
    }
    
    /**
     * 包养主绑定统计
     * @param int $userID 包养主ID
     * @param int $groupID 红利套餐ID
     */
    public static function bindCount($userID, $groupID){
        return self::find()->where(['belong'=>$userID, 'user_group_id'=>$groupID])->count();
    }
    
    /**
     * 创建浏览用户
     * @param int $openID 微信身份标识
     * @param int $equipmentID 设备主键
     * @return boolean
     */
    public static  function create($openID, $equipmentID){
        $equip = "";
        if(!empty($equipmentID)){
            $equipArray = explode("_", $equipmentID);
            if(isset($equipArray[1]) && $equipArray[1] != '0'){
                $equip = $equipArray[1];
            }
        }
        $user = User::findOne(["openid"=>$openID]);
        if(!$user){
            $user = new User();
            $user->openid = $openID;
            $user->role = self::ROLE_USER_COMMON;
            $user->status = self::STATUS_DELETED;
            $user->created_at = time();
        }
        $user->equipment_id = $equip;
        $user->save();
    }
    
}
