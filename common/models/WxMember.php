<?php

namespace common\models;

use backend\models\DistributionUser;
use backend\models\DistributionUserSchedule;
use backend\models\Manager;
use backend\models\Organization;
use backend\models\ScmSupplier;
use common\models\Api;
use Yii;
use Yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "wx_member".
 *
 * @property string $userid
 * @property string $name
 * @property string $position
 * @property string $mobile
 * @property integer $gender
 * @property string $email
 * @property string $weixinid
 * @property string $avatar_mediaid
 * @property string $extattr
 * @property integer $create_time
 * @property integer $department_id
 * @property string $parent_id
 * @property string $parent_path
 * @property integer $org_id
 *
 * @property DistributionTask[] $distributionTasks
 * @property Manager[] $managers
 * @property WxMemberTagAssoc[] $wxMemberTagAssocs
 * @property WxTag[] $wxTags
 */
class WxMember extends \yii\db\ActiveRecord
{
    /** 是否删除常量定义 */
    // 未删除
    const DEL_NO = 1;
    // 已删除
    const DEL_YES = 2;

    /** 成员职位常量 */
    // 设备经理
    const EQUIP_MANAGER = 1;
    // 设备主管
    const EQUIP_RESPONSIBLE = 2;
    // 设备人员
    const EQUIP_MEMBER = 3;
    // 运维助理
    const EQUIP_ASSISTANT = 14;
    // 配送经理
    const DISTRIBUTION_MANAGER = 4;
    // 配送主管
    const DISTRIBUTION_RESPONSIBLE = 5;
    // 配送人员
    const DISTRIBUTION_MEMBER = 6;
    // 供应链经理
    const SUPPLY_CHAIN_MANAGER = 7;
    // 库管
    const KUGUAN = 8;
    // 客服
    const CUSTOMER_SERVICE = 9;
    // 销售助理
    const SALE_ASSISTANT = 10;
    // 销售人员
    const SALE_MEMBER = 11;
    // CEO
    const CEO = 12;
    // COO
    const COO = 13;
    // 投放商
    const TRAFFICKING_SUPPLIERS = 15;
    // 供水商
    const WATERSUPPLIERS = 16;
    // 市场总监
    const MARKETING_DIRECTOR = 17;
    // CTO
    const CTO = 18;
    // 测试人员
    const TEST = 19;
    // 外卖配送人员
    const DELIVERY_PERSON = 20;
    // 运营总监
    const OPERATION_DIRECTOR = 21;
    // 运营经理
    const OPERATION_MANAGER = 22;
    // 运营主管
    const OPERATION_RESPONSIBLE = 23;
    // 运营人员
    const OPERATION_MEMBER = 24;

    // 产品总监
    const PRODUCT_DIRECTOR = 25;
    // 产品经理
    const PRODUCT_MANAGER = 26;
    // 产品主管
    const PRODUCT_RESPONSIBLE = 27;
    // 产品人员
    const PRODUCT_MEMBER = 28;

    //机构名称数组
    public $orgArr;

    public static $position = [
        ''                             => '请选择',
        self::EQUIP_MANAGER            => '设备经理',
        self::EQUIP_RESPONSIBLE        => '设备主管',
        self::EQUIP_MEMBER             => '设备人员',
        self::DISTRIBUTION_MANAGER     => '配送经理',
        self::DISTRIBUTION_RESPONSIBLE => '配送主管',
        self::DISTRIBUTION_MEMBER      => '配送人员',
        self::SUPPLY_CHAIN_MANAGER     => '供应链经理',
        self::KUGUAN                   => '库管',
        self::CUSTOMER_SERVICE         => '客服',
        self::SALE_ASSISTANT           => '销售助理',
        self::SALE_MEMBER              => '销售人员',
        self::CEO                      => 'CEO',
        self::COO                      => 'COO',
        self::CTO                      => 'CTO',
        self::TRAFFICKING_SUPPLIERS    => '投放商',
        self::WATERSUPPLIERS           => '供水商',
        self::MARKETING_DIRECTOR       => '市场总监',
        self::TEST                     => '测试人员',
        self::DELIVERY_PERSON          => '外卖配送人员',
        self::OPERATION_DIRECTOR       => '运营总监',
        self::OPERATION_MANAGER        => '运营经理',
        self::OPERATION_RESPONSIBLE    => '运营主管',
        self::OPERATION_MEMBER         => '运营人员',
        self::PRODUCT_DIRECTOR         => '产品总监',
        self::PRODUCT_MANAGER          => '产品经理',
        self::PRODUCT_RESPONSIBLE      => '产品主管',
        self::PRODUCT_MEMBER           => '产品人员',
    ];

    /**
     *  职位对应微信模块权限。
     *  设备投放商 --- 8
     *  供水商     --- 7
     *  设备管理   --- 6
     *  配送应用   --- 4
     **/
    public static $positionWeChat = [
        self::SALE_MEMBER              => 0,
        self::EQUIP_MANAGER            => 6,
        self::EQUIP_RESPONSIBLE        => 6,
        self::EQUIP_MEMBER             => 6,
        self::SUPPLY_CHAIN_MANAGER     => 6,
        self::DISTRIBUTION_MANAGER     => 4,
        self::DISTRIBUTION_RESPONSIBLE => 4,
        self::DISTRIBUTION_MEMBER      => 4,
        self::WATERSUPPLIERS           => 7,
        self::TRAFFICKING_SUPPLIERS    => 8,

    ];

    /**
     * 定义配送人员职位列表
     * @var [type]
     */
    public static $disPositionArr = [
        self::DISTRIBUTION_MANAGER,
        self::DISTRIBUTION_RESPONSIBLE,
        self::DISTRIBUTION_MEMBER,
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wx_member';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'name', 'department_id', 'position'], 'required'],
            [['org_id'], 'required', 'on' => ['create', 'update']],
            [['gender', 'create_time', 'department_id', 'org_id', 'supplier_id', 'is_del'], 'integer'],
            [['userid', 'name', 'email', 'weixinid', 'parent_id'], 'string', 'max' => 64],
            [['mobile'], 'string', 'max' => 11],
            [['avatar_mediaid'], 'string', 'max' => 300],
            [['extattr'], 'string', 'max' => 1000],
            [['parent_path'], 'string', 'max' => 200],
            [['email'], 'email'],
            [['mobile'], 'mobileCheck'],
            [['mobile'], 'unique'],
            [['userid'], 'unique'],
            [['weixinid'], 'unique'],
            [['supplier_id'], 'default', 'value' => 0],
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
            'userid'         => '用户账号',
            'name'           => '用户名',
            'position'       => '职位',
            'mobile'         => '手机号',
            'gender'         => '性别',
            'email'          => '邮箱',
            'weixinid'       => '微信号',
            'avatar_mediaid' => '成员头像url',
            'extattr'        => '扩展属性',
            'create_time'    => '添加时间',
            'department_id'  => '所在部门',
            'tag'            => '选择标签',
            'parent_id'      => '成员直属领导',
            'parent_path'    => '成员路径',
            'org_id'         => '成员所在分公司',
            'supplier_id'    => '供应商',
        ];
    }

    /**
     * 获取成员详情
     * @param  string $field 要查询的字段 如：'id,name'
     * @param  array $where 查询条件 如：array('id'=>1)
     * @return array        成员详情
     */
    public static function getMemberDetail($field = '*', $where = array())
    {
        return self::find()->select($field)->where($where)->andWhere(['is_del' => self::DEL_NO])->asArray()->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(Organization::className(), ['org_id' => 'org_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDistributionTasks()
    {
        return $this->hasMany(DistributionTask::className(), ['delivery_userid' => 'userid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManagers()
    {
        return $this->hasMany(Manager::className(), ['userid' => 'userid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWxMemberTagAssocs()
    {
        return $this->hasMany(WxMemberTagAssoc::className(), ['wx_memberid' => 'userid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWxTags()
    {
        return $this->hasMany(WxTag::className(), ['tagid' => 'wx_tagid'])->viaTable('wx_member_tag_assoc', ['wx_memberid' => 'userid']);
    }

    public function getSupplier()
    {
        return $this->hasOne(ScmSupplier::className(), ['id' => 'supplier_id']);
    }

    public function getDistributionUser()
    {
        return $this->hasOne(DistributionUser::className(), ['userid' => 'userid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(WxDepartment::className(), ['id' => 'department_id']);
    }

    /**
     * 根据标签id获取用户id和用户名称
     * @param  $type 1添加标签用户使用 2删除标签用户使用
     * @param  integer
     * @return [type]
     */
    public static function getMembers($tagid, $type = 1)
    {
        $where = [];
        //获取该标签现有用户列表
        $tagUserList = \common\models\WxMemberTagAssoc::getMemberVal($tagid);
        if ($type == 2 && empty($tagUserList)) {
            return array();
        }

        if (!empty($tagUserList) && $type == 1) {
            $where = ['and', ['is_del' => 1], ['not in', 'userid', $tagUserList]];
        }

        if (!empty($tagUserList) && $type == 2) {
            $where = ['and', ['is_del' => 1], ['in', 'userid', $tagUserList]];
        }

        // 获取该标签中没有的用户列表
        $res      = self::getMemberList('userid,name', $where);
        $userList = array();
        foreach ($res as $v) {
            $userList[$v->userid] = $v->name;
        }
        return $userList;
    }

    /**
     * 获取用户名称
     * @param  array $userid 用户id列表
     * @return [type]
     */
    public static function getMemberName($userid)
    {
        $res      = self::getMemberList('name', ['userid' => $userid]);
        $username = [];
        foreach ($res as $key => $value) {
            $username[] = $value->name;
        }
        return $username;
    }

    /**
     * 获取未删除的人员id 和相关信息
     * @author  zmy
     * @version 2016-12-20
     * @return  [type]     [description]
     */
    public static function getMemberNameInfoArr($where = ['is_del' => 1])
    {
        $memberList     = self::getMemberList('*', $where);
        $memberNameList = array('' => '请选择');
        foreach ($memberList as $key => $value) {
            $positionName                   = self::$position[$value->position] ?? '';
            $memberNameList[$value->userid] = $value->name . '-' . $positionName . '-' . $value->name;
        }
        return $memberNameList;
    }

    /**
     * 获取用户工作状态
     * @param  $userid 人员id
     * @param  $date 日期 格式yyyy-mm-dd
     * @return string     人员工作状态码1上班2休息3请假
     */
    public static function getUserScheduleStatus($userid, $date)
    {
        $status       = [];
        $scheduleInfo = [];
        $yearMonth    = date('Y-m', $date);
        $day          = date('d', $date);
        //查询人员是否上班
        $scheduleInfo = DistributionUserSchedule::getUserScheduleList($yearMonth, $userid);
        if (empty($scheduleInfo)) {
            return 2;
        }
        $schedule = $scheduleInfo[0]['schedule'];
        $parten   = '/' . $day . '-\d{1}/';
        preg_match_all($parten, $schedule, $matches);
        $status = explode('-', $matches[0][0]);
        return $status[1];
    }

    /**
     * 获取用户列表
     * @param  string $filed 要查寻的字段 如：'userid,name'
     * @param  array $where 查询条件 如：array('id'=>1)
     * @return array        楼宇列表
     */
    public static function getWxMemberUserList($filed = "*", $where = array())
    {
        return self::find()->select($filed)->where($where)->andWhere(['is_del' => self::DEL_NO])->asArray()->all();
    }

    /**
     * 获取用户列表 单个数组
     * @param  string $filed 要查寻的字段 如：'userid,name'
     * @param  array $where 查询条件 如：array('id'=>1)
     * @return array        楼宇列表
     */
    public static function getWxMemberNameList($filed = "*", $where = array())
    {
        return self::find()->select($filed)->where($where)->andWhere(['is_del' => self::DEL_NO])->asArray()->one();
    }

    /**
     * 获取成员列表
     * @param  string $field 要查询的字段 如：'id,name'
     * @param  array $where 查询条件 如：array('id'=>1)
     * @return array        成员列表
     */
    public static function getMemberList($field = '*', $where = array())
    {
        return self::find()->select($field)->where($where)->andWhere(['is_del' => self::DEL_NO])->all();
    }
    /**
     * 获取当前用户及其下级的成员id
     * @return [type] [description]
     */
    public static function getUseridFromParent($userid, $type = 1)
    {
        $child_userids = [];
        //根据用户等级获取用户等级路径
        $memberDetail = self::getMemberDetail('parent_path', ['userid' => $userid]);
        if (!$memberDetail) {
            return [];
        }

        $parent_path = $memberDetail['parent_path'];
        //当前用户的下级用户id
        $child_userids = self::getMemberNameList(['like', 'parent_path', $parent_path]);
        return $child_userids;
    }

    /**
     * 根据条件获取成员名称列表
     * @param  array $where 查询条件 如：array('id'=>1)
     * @return array        成员名称数组
     */
    public static function getMemberNameList($where = array())
    {
        $memberList     = self::getMemberList('userid,name', $where);
        $memberNameList = array('' => '请选择');
        foreach ($memberList as $key => $value) {
            $memberNameList[$value->userid] = $value->name;
        }
        return $memberNameList;
    }
    /**
     * 根据条件获取成员名称列表
     * @param  array $where 查询条件 如：array('id'=>1)
     * @return array        成员名称数组
     */
    public static function getDeliveryNameList($where = array(), $name)
    {
        $memberList     = self::getMemberList('userid,name', $where);
        $personList     = DeliveryApi::getDeliveryPersonList();
        $personList     = array_combine(array_column($personList['list'], 'wx_number'), $personList['list']);
        $memberNameList = array('' => '请选择');
        foreach ($memberList as $key => $value) {
            if (empty($personList[$value->userid]) || $value->name == $name) {
                $memberNameList[$value->name] = $value->name;
            }
        }
        return $memberNameList;
    }
    /**
     * 获取成员名称
     * @param  [type] $userid [description]
     * @return [type]         [description]
     */
    public static function getWxMemberName($userid)
    {
        $str = '';
        if ($userid) {
            $useridArr = explode(',', $userid);
            foreach ($useridArr as $key => $value) {
                $memberDetail = self::findOne($value);
                if (!$memberDetail) {
                    continue;
                }

                $positionId = $memberDetail->position;
                if (!$positionId) {
                    continue;
                }

                $positionName = isset(self::$position[$positionId]) ? self::$position[$positionId] : '';
                $str .= $positionName . '-' . $memberDetail->name . '，';
            }
            $str = trim($str, '，');
        }
        return $str;
    }

    /**
     * 获取投放商列表
     * @param  integer $headquarter 投放商标识
     * @param  integer $org_id      分公司id
     * @return array
     */
    public static function getTraffickingSuppliers($userid = '')
    {
        $traffickUseridArr = EquipTraffickingSuppliers::getColumn('userid');
        if ($userid && $traffickUseridArr) {
            $traffickUseridArr = array_flip($traffickUseridArr);
            unset($traffickUseridArr[$userid]);
            $traffickUseridArr = array_flip($traffickUseridArr);
        }
        //获取投放商成员
        return self::getMemberNameList(['and', ['position' => self::TRAFFICKING_SUPPLIERS], ['not in', 'userid', $traffickUseridArr]]);
    }

    /**
     * 根据分公司id获取职位为配送主管、设备主管、运维助理的成员列表（异常报警中报警对象）
     * @return [type] [description]
     */
    public static function getMemberFromPosition()
    {
        // 查询职位为配送主管、设备主管、运维助理的成员
        $where = ['or', 'position = ' . self::DISTRIBUTION_RESPONSIBLE, 'position = ' . self::EQUIP_RESPONSIBLE, 'position = ' . self::EQUIP_ASSISTANT];
        // 获取当前登录用户所在分公司id
        $org_id = Manager::getManagerBranchID();
        // 判断如果不是总公司id则按照分公司搜索
        if ($org_id > 1) {
            $where = ['and', ['org_id' => $org_id], $where];
        }
        return self::getPostionMemberList($where);
    }

    /**
     * 获取成员对应公司、职位列表
     * @return [type] [description]
     */
    public static function getPostionMemberList($where = [])
    {
        $memberList = self::getMemberList('userid,position,name,org_id', $where);
        if (!$memberList) {
            return [];
        }

        $memberNameList = [];
        foreach ($memberList as $key => $value) {
            $memberNameList[$value->userid] = $value->position ? $value->organization->org_name . '-' . self::$position[$value->position] . '-' . $value->name : $value->name;
        }
        return $memberNameList;
    }

    /**
     * 获取该成员所有上级用户id组
     * @param  string $userid 成员用户id
     * @return array         该成员所有上级用户id组
     */
    public static function memberLevel($userid = '')
    {
        $parent_path              = self::findOne($userid)->parent_path;
        $parent_path              = trim($parent_path, '-');
        $parent_path_arr          = explode('-', $parent_path);
        $parent_path_distinct_arr = array_unique($parent_path_arr);
        return $parent_path_distinct_arr;
    }
    /**
     * 获取该会员的上级个数
     * @param  string $userid 成员用户id
     * @return int         上级个数
     */
    public static function memberLevelNum($userid = '')
    {
        $parent_arr = self::memberLevel($userid);
        return count($parent_arr);
    }

    /**
     * 获取该会员对应上级级数和对应上级名称
     * @param  string $userid 成员id
     * @return array         等级=>成员名称
     */
    public static function memberLevelName($userid = '')
    {
        $levelNameArr = [];
        //翻转查询出来的数组
        $parant_arr = array_reverse(self::memberLevel($userid));

        foreach ($parant_arr as $k => $v) {
            $model            = self::findOne($v);
            $levelNameArr[$k] = self::$position[$model->position] . '-' . $model->name;
        }
        return $levelNameArr;
    }

    /**
     * 获取指定等级的用户id
     * @param  string  $userid    [description]
     * @param  integer $reportnum [description]
     * @return [type]             [description]
     */
    public static function memberLevelUserid($userid = '', $reportnum = 1)
    {
        $userArr = [];
        //获取该成员等级
        $levelArr = array_reverse(self::memberLevel($userid));
        $userArr  = array_slice($levelArr, 1, $reportnum);
        return $userArr;
    }

    /**
     * 根据分公司获取除组长外的所有配送员（人员分配时使用）
     * @param  string $org_id [description]
     * @return [type]         [description]
     */
    public static function getDistributionUsers($org_id = '')
    {
        return self::getMemberNameList(['and', ['org_id' => $org_id], ['in', 'position', [self::DISTRIBUTION_MEMBER, self::DISTRIBUTION_RESPONSIBLE]]]);
    }

    /**
     * 获取运维人员编组信息
     * @param  array   userid [description]
     * @return [type]         [description]
     */
    public static function getUserGroupInfo($userid)
    {
        return self::find()
            ->alias('wx')
            ->leftJoin('distribution_user du', 'wx.userid = du.userid')
            ->andWhere(['!=', 'wx.is_del', 2])
            ->andWhere(['wx.userid' => $userid])
            ->orderBy('group_id ASC,is_leader ASC,userid ASC')
            ->select(['wx.name', 'wx.userid', 'du.leader_id', 'du.is_leader', 'du.group_id'])
            ->asArray()
            ->all();
    }
    /**
     * 根据userid获取运维人员排班信息(人员管理时使用)
     * @param  array   userid [description]
     * @return [type]         [description]
     */
    public static function getSchedule($userid, $date)
    {
        $userSchedule = self::find()->orderBy('du.group_id ASC,du.userid ASC')
            ->alias('wx')
            ->leftJoin('distribution_user du', 'du.userid = wx.userid')
            ->leftJoin('distribution_user_schedule dc', 'dc.userid = du.userid')
            ->andWhere(['wx.is_del' => self::DEL_NO])
            ->andWhere(['wx.userid' => $userid])
            ->andWhere(['dc.date' => $date])
            ->select('wx.name,wx.userid,du.group_id,dc.schedule')
            ->asArray()
            ->all();
        foreach ($userSchedule as $key => $schedule) {
            $userSchedule[$key]['schedule'] = explode('|', $schedule['schedule']);
        }
        return $userSchedule;
    }

    /**
     * 获取运维人员组长(人员管理时使用)
     * @author wangxiwen
     * @version 2018-10-17
     * @param  array $userGroup 运维人员组别信息
     * @return array 运维人员组长信息
     */
    public static function getUserLeader($userGroup)
    {
        $managerArray = [];
        foreach ($userGroup as $user) {
            if ($user['is_leader'] == 1) {
                $managerArray[] = [
                    'name'     => $user['name'],
                    'userid'   => $user['userid'],
                    'group_id' => $user['group_id'],
                ];
            }
        }
        return $managerArray;
    }

    /**
     * 获取当前登录用户所在分公司的配送员userid列表
     * @param  integer $org_id [description]
     * @return [type]          [description]
     */
    public static function getMemberIDArr($org_id = 0)
    {
        $department_id = WxDepartment::getDepartIds(WxDepartment::DISTRIBUTION_DEPARTMENT, $org_id);

        $userList = self::getMemberList('userid', ['department_id' => $department_id]);

        $useridArr = [];

        foreach ($userList as $k => $v) {
            $useridArr[] = $v->userid;
        }

        return $useridArr;
    }

    /**
     * 根据楼宇id获取成员列表
     * @param  string $build_id 楼宇id
     * @param  intval $position 职位标识
     * @return [type]           [description]
     */
    public static function getMemberIDNameArr($build_id = '', $position = [], $position_manager = '')
    {
        if (!$build_id) {
            return ['' => '请选择'];
        }

        $org_id = Building::findOne(['id' => $build_id])->org_id;
        $where  = ['or', ['and', ['org_id' => $org_id], $position], ['position' => $position_manager]];
        return self::getMemberNameList($where);
    }

    /**
     * 根据分公司id获取配送人员列表
     * @param  string $orgId 分公司id
     * @param  intval $position 职位标识
     * @return [type]           [description]
     */
    public static function getDistributionUserArr($type = 1, $orgId = '')
    {
        $orgId = $orgId ? $orgId : Manager::getManagerBranchID();
        if ($type == 1) {
            // 只获取配送人员列表
            $where = $orgId > 1 ? ['org_id' => $orgId, 'position' => self::DISTRIBUTION_MEMBER] : ['position' => self::DISTRIBUTION_MEMBER];
        } else if ($type == 2) {
            // 获取配送人员和配送主管
            $where = $orgId > 1 ? ['and', ['org_id' => $orgId], ['or', ['position' => self::DISTRIBUTION_RESPONSIBLE], ['position' => self::DISTRIBUTION_MEMBER]]] : ['or', ['position' => self::DISTRIBUTION_RESPONSIBLE], ['position' => self::DISTRIBUTION_MEMBER]];
        } else if ($type == 3) {
            // 获取配送人员、配送主管、配送经理(总公司时显示)
            $where = $orgId > 1 ? ['and', ['org_id' => $orgId], ['or', ['position' => self::DISTRIBUTION_RESPONSIBLE], ['position' => self::DISTRIBUTION_MEMBER]]] : ['or', ['position' => self::DISTRIBUTION_RESPONSIBLE], ['position' => self::DISTRIBUTION_MEMBER], ['position' => self::DISTRIBUTION_MANAGER]];
        }
        $equipDisUserList = self::getMemberList('*', $where);
        // $equipDisUserList = self::getMemberNameList($where);
        return self::getCanUser($equipDisUserList);
    }

    /**
     * 去除不可接单的配送员列表
     * @author  zgw
     * @version 2016-10-28
     * @param   [type]     $userList [description]
     * @return  [type]               [description]
     */
    private static function getCanUser($userList)
    {
        $canUserList = ['' => '请选择'];
        foreach ($userList as $userObj) {
            if (isset($userObj->distributionUser->user_status)) {
                if ($userObj->distributionUser->user_status != DistributionUser::WORK_ON) {
                    continue;
                }
            }
            $canUserList[$userObj->userid] = $userObj->name;
        }
        return $canUserList;
    }

    /**
     * 获取指定某个成员的姓名
     * @author  zgw
     * @version 2016-08-16
     * @param   string     $userid 成员id
     * @return  string             成员姓名
     */
    public static function getNameOne($userid)
    {
        $model = self::findOne($userid);
        return $model ? $model->name : '';
    }

    /**
     * 根据用户id获取分公司id
     * @author  zgw
     * @version 2016-08-11
     * @param   integer     $id 用户
     * @return  integer     分公司id
     */
    public static function getOrgId($id)
    {
        $wxMemberModel = self::findOne($id);
        return $wxMemberModel ? $wxMemberModel->org_id : 0;
    }

    /**
     * 获取某个字段的值
     * @author  zgw
     * @version 2016-09-05
     * @param   [type]     $filed [description]
     * @param   [type]     $where [description]
     * @return  [type]            [description]
     */
    public static function getFiled($filed, $where)
    {
        $memberObj = self::find()->where($where)->andWhere(['is_del' => self::DEL_NO])->one();
        return $memberObj ? $memberObj->$filed : '';
    }

    /**
     * 获取当前用户所在分公司下的成员
     * @author  zgw
     * @version 2016-09-09
     * @param   string     $org_id [description]
     * @param   [type]     $where  [description]
     * @return  [type]             [description]
     */
    public static function getMemberArrFormOrgid($where)
    {
        $org_id = Manager::getManagerBranchID();
        if ($org_id > 1) {
            $where = ['and', ['org_id' => $org_id], $where];
        }
        return self::getMemberNameList($where);
    }

    /**
     * 获取用户id
     * @author  zgw
     * @version 2016-09-13
     * @param   [type]     $where [description]
     * @return  [type]            [description]
     */
    public static function getUserIdArr($where)
    {
        return \yii\helpers\ArrayHelper::getColumn(self::find()->where($where)->all(), 'userid');
    }

    /**
     * 获取在职该分公司的配送人员列表（包含配送经理和配送主管）
     * @author  zgw
     * @version 2016-09-14
     * @param   $type      1-获取设备加人员 2-获取配送人员 3-配送加设备人员
     * @return  [type]     [description]
     */
    public static function distributionIdNameArr($orgId, $type = 1)
    {
        $orgCondition = ['org_id' => $orgId];
        //查询该公司是否是代维护,代维护查出父公司下的相关人员
        $organizationInfo = Api::getOrgDetailsModel(['org_id' => $orgId]);
        //代维护
        if (isset($organizationInfo['is_replace_maintain']) && $organizationInfo['is_replace_maintain'] == 2) {
            $orgIdList    = array_filter(explode('-', $organizationInfo['parent_path']));
            $orgCondition = ['org_id' => $orgIdList];
        }
        $equipWhere = $orgId <= 2 ? ['position' => WxMember::EQUIP_MANAGER] : [];
        $distrWhere = $orgId <= 2 ? ['position' => WxMember::DISTRIBUTION_MANAGER] : [];
        if ($type == 1) {
            // 设备人员
            $where = ['or', ['and', $orgCondition, ['or', ['position' => WxMember::EQUIP_MEMBER], ['position' => WxMember::EQUIP_RESPONSIBLE]]], $equipWhere];
        } else if ($type == 2) {
            // 获取配送人员
            $where = ['or', ['and', $orgCondition, ['or', ['position' => WxMember::DISTRIBUTION_MEMBER], ['position' => WxMember::DISTRIBUTION_RESPONSIBLE]]], $distrWhere];
        } else {
            // 配送加设备人员
            $where = ['or', ['and', $orgCondition, ['or', ['position' => WxMember::DISTRIBUTION_MEMBER], ['position' => WxMember::DISTRIBUTION_RESPONSIBLE], ['position' => WxMember::EQUIP_MEMBER], ['position' => WxMember::EQUIP_RESPONSIBLE]]], $distrWhere, $equipWhere];
        }
        return self::getMemberList('userid, name', $where);
    }

    /**
     * 根据配送人员所在分公司获取设备和配送人员列表
     * @author  zgw
     * @version 2016-11-10
     * @param   [type]     $orgId [description]
     * @return  [type]            [description]
     */
    public static function equipDistributionIdNameArr($orgId = '')
    {
        $orgId = $orgId ? $orgId : Manager::getManagerBranchID();
        if ($orgId > 1) {
            $equipWhere = $orgId == 2 ? ['position' => self::EQUIP_MANAGER] : [];
            $distrWhere = $orgId == 2 ? ['position' => self::DISTRIBUTION_MANAGER] : [];
            $where      = ['or', ['and', ['org_id' => $orgId], ['or', ['position' => self::DISTRIBUTION_MEMBER], ['position' => self::DISTRIBUTION_RESPONSIBLE], ['position' => self::EQUIP_MEMBER], ['position' => self::EQUIP_RESPONSIBLE]]], $distrWhere, $equipWhere];
        } else {
            $positionArr = [self::EQUIP_MANAGER, self::EQUIP_RESPONSIBLE, self::EQUIP_MEMBER, self::DISTRIBUTION_MANAGER, self::DISTRIBUTION_MEMBER, self::DISTRIBUTION_RESPONSIBLE];
            $where       = ['position' => $positionArr];
        }
        return self::getMemberNameList($where);
    }

    /**
     * 获取配送人员和设备是人员数组
     * @author  zgw
     * @version 2016-11-21
     * @param   [type]     $orgId [description]
     * @param   integer    $type  [description]
     * @return  [type]            [description]
     */
    public static function equipDisUserArr($orgId, $type = 1)
    {
        $userArr  = ['' => '请选择'];
        $userList = self::distributionIdNameArr($orgId, $type);
        foreach ($userList as $userObj) {
            if (isset($userObj->distributionUser->user_status)) {
                if ($userObj->distributionUser->user_status == DistributionUser::WORK_ON) {
                    $userArr[$userObj->userid] = $userObj->name;
                }
            } else {
                $userArr[$userObj->userid] = $userObj->name;
            }
        }
        return $userArr;
    }

    /**
     * 根据分公司id获取配送主管id
     * @author  zgw
     * @version 2017-04-28
     * @param   int     $orgId 分公司id
     * @return  string         用户id
     * @param $orgId
     * @param string $field
     * @return false|null|string
     */
    public static function getDisResponsibleFromOrg($orgId, $field = 'userid')
    {
        return self::find()->select($field)->where(['org_id' => $orgId, 'position' => self::DISTRIBUTION_RESPONSIBLE, 'is_del' => self::DEL_NO])->scalar();
    }

    /**
     * 根据分公司ID查询配送主管配送经理
     * @param $orgId
     * @param string $field
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getRoleByOrg($orgId, $field = 'userid')
    {
        return self::find()->select($field)->where(['org_id' => $orgId, 'position' => [self::DISTRIBUTION_RESPONSIBLE, self::DISTRIBUTION_MANAGER], 'is_del' => self::DEL_NO])->asArray()->all();
    }

    /**
     * 根据分公司ID获取设备主管和设备经理
     * @author wxl
     * @param $orgId
     * @param string $field
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getEquipmentOwnerByOrganization($orgId, $field = 'userid')
    {
        return self::find()->select($field)->where(['org_id' => $orgId, 'position' => [self::EQUIP_RESPONSIBLE, self::EQUIP_MANAGER], 'is_del' => self::DEL_NO])->asArray()->all();
    }

    /**
     * 判断人员是否是公司的配送主管或经理
     * @author wxl
     * @param int $organizationId
     * @param int $userId
     * @return bool
     */
    public static function isOrganizationManager($organizationId = 0, $userId = 0)
    {
        //查询公司配送主管配送经理
        $distributionManager = WxMember::getRoleByOrg($organizationId);
        //查询公司设备主管设备经理
        $equipmentManager        = WxMember::getEquipmentOwnerByOrganization($organizationId);
        $distributionManagerList = $distributionManager ? ArrayHelper::getColumn($distributionManager, 'userid') : [];
        $equipmentManagerList    = $equipmentManager ? ArrayHelper::getColumn($equipmentManager, 'userid') : [];
        return $userId ? in_array($userId, array_merge($distributionManagerList, $equipmentManagerList)) : false;
    }

    /**
     * 判断该用户是否存在
     * @author sulingling
     * @param  $tel
     * @return Array()  ?   Boolean
     */
    public static function getOne($where)
    {
        $data = self::find()->where($where)->one();
        return $data ? $data : false;
    }

    /**
     * 获取运维人员数据
     * @author  zmy
     * @version 2018-04-04
     * @return  [type]     [description]
     */
    public static function getDistributionList()
    {
        $distributionList = self::find()->where(['position' => self::DISTRIBUTION_MEMBER, 'is_del' => 1])->all();
        $list             = [];
        foreach ($distributionList as $key => $value) {
            $list[$key]['userid'] = $value->userid;
            $list[$key]['name']   = $value->name;
        }
        return $list;
    }

    /**
     * 获取运维人员id=>name数组
     * @author  zmy
     * @version 2018-04-08
     * @return  [type]     [description]
     */
    public static function getDistributionIdToNameList($isSelect = 0)
    {
        $distributionList = self::find()->where(['position' => self::DISTRIBUTION_MEMBER, 'is_del' => 1])->all();
        if ($isSelect) {
            $list = ['' => '请选择'];
        } else {
            $list = [];
        }

        foreach ($distributionList as $key => $value) {
            $list[$value->userid] = $value->name;
        }
        return $list;
    }

    /**
     * 获取当月天数
     * @param $year
     * @param $month
     * @return
     */
    public static function getDays($year, $month)
    {
        //判断月份是否是两位
        $month = $month < 10 ? str_replace(0, '', $month) : $month;
        if (in_array($month, array(1, 3, 5, 7, 8, 10, 12))) {
            $text = 31;
        } elseif ($month == 2) {
            if ($year % 400 == 0 || ($year % 4 == 0 && $year % 100 !== 0)) //判断是否是闰年
            {
                $text = 29;
            } else {
                $text = 28;
            }
        } else {
            $text = 30;
        }
        return $text;
    }

    /**
     * 通过运维人员id获取运维人员名称
     * @author wangxiwen
     * @version 2018-5-8
     * @return [type] [description]
     */
    public static function getUserName($userid)
    {
        return self::find()->where(['userid' => $userid])->select('name')->one();
    }

    /**
     * 插入排班数据
     * @param $date
     * @param $userid
     * @param $status
     * @return
     */
    public static function insertScheduleData($date, $userid, $status)
    {
        //获取月份天数
        $dateArr = explode('-', $date);
        $days    = self::getDays($dateArr[0], $dateArr[1]);
        $str     = '';
        for ($i = 0; $i < $days; $i++) {
            if ($i < 9) {
                $str .= '0' . ($i + 1) . '-' . $status . '|';
            } else {
                $str .= ($i + 1) . '-' . $status . '|';
            }
        }
        $str             = substr($str, 0, -1);
        $query           = new DistributionUserSchedule();
        $query->userid   = $userid;
        $query->date     = $date;
        $query->schedule = $str;
        return $query->save();
    }

    /**
     * 人员管理首页数据
     * @author wangxiwen
     * @version 2018-4-17
     * @param int $orgId 所属公司ID
     * @return [type] [description]
     */
    public static function getManagement($orgId)
    {
        //获取运维人员列表
        $userid = self::getMemberIDArr($orgId);
        //运维人员组别信息
        $userGroup = self::getUserGroupInfo($userid);
        //获取运维人员组长列表
        $userLeader = self::getUserLeader($userGroup);
        //获取最大组别数
        // $groupNum    = ceil(count($distributionList) / 7);
        $groupNum    = 10;
        $groupNumber = self::getGroupNumber($groupNum);
        $dateList    = [
            'year'  => date('Y'),
            'month' => date('m'),
            'days'  => self::getDays(date('Y'), date('m')),
        ];
        $date = date('Y-m');
        //获取排班管理数据
        $userSchedule = self::getSchedule($userid, $date);
        $model        = Json::encode([
            'groupInfo'    => $userGroup,
            'groupNumber'  => $groupNumber,
            'managers'     => $userLeader,
            'scheduleInfo' => $userSchedule,
            'date'         => $dateList,
            'isChange'     => 1,
        ]);

        return $model;
    }

    /**
     * 获取组别
     * @author wangxiwen
     * @version 2018-10-17
     * @param int $number 数量
     * @return array
     */
    private static function getGroupNumber($number)
    {
        $groupNumber = [];
        for ($i = 0; $i <= $number; $i++) {
            if ($i == 0) {
                $groupNumber[0][] = '未分组';
            } else {
                $groupNumber[$i][] = $i . '组';
            }
        }
        return $groupNumber;
    }

    /**
     * 通过openId获取userid
     * @author wangxiwen
     * @version 2018-08-27
     * @param  [string] $openId
     * @return [string] [用户id]
     */
    public static function getUseridByOpenid($openId)
    {
        return self::find()
            ->where(['openID' => $openId])
            ->select('userid')
            ->asArray()
            ->scalar();
    }

    /**
     * 获取运维主管
     * @author wangxiwen
     * @version 2018-10-13
     * @return array
     */
    public static function getDistributeDirector()
    {
        //获取运维人员关系数据
        $directorArray = self::find()
            ->select('userid,org_id')
            ->where(['position' => self::DISTRIBUTION_RESPONSIBLE])
            ->asArray()
            ->all();
        $directorList = [];
        foreach ($directorArray as $director) {
            $directorList[$director['org_id']][] = $director['userid'];
        }
        return $directorList;
    }

    /**
     * 获取成员名称
     * @author wangxiwen
     * @version 2018-11-13
     * @return
     */
    public static function getUserInfo($type = 1)
    {
        $where     = $type ? ['is_del' => self::DEL_NO] : [];
        $userArray = self::find()
            ->select('userid,name')
            ->where($where)
            ->asArray()
            ->all();
        return ArrayHelper::map($userArray, 'userid', 'name', null);
    }

    /**
     * 根据用户ID获取用户ID和名称
     * @author zhenggangwei
     * @date   2019-01-31
     * @param  array     $useridInfoArr 用户ID
     * @return array
     */
    public static function getUserIdNameByUserId($useridInfoArr)
    {
        return self::find()
            ->andWhere(['in', 'userid', $useridInfoArr])
            ->select('userid,name')
            ->asArray()
            ->all();
    }

    /**
     * 根据用户ID获取机构ID
     * @author zhenggangwei
     * @date   2019-05-30
     * @param  integer     $userId 用户ID
     * @return integer             机构ID
     */
    public static function getOrgIdByUserId($userId)
    {
        return self::find()
            ->where(['userid' => $userId])
            ->select('org_id')
            ->scalar();
    }
}
