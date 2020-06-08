<?php

namespace backend\models;

use backend\models\BuildingRecord;
use backend\models\Manager;
use common\helpers\WXApi\MediaImg;
use common\models\BuildingRecordApi;
use common\models\WxMember;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "building_record".
 *
 * @property int $id
 * @property int $creator_id 楼宇创建人ID
 * @property string $creator_name 楼宇创建人姓名
 * @property int $org_id 楼宇创建人所在分公司
 * @property string $building_name 楼宇名称（建筑物名称）
 * @property int $build_type_id 楼宇类型ID
 * @property int $building_status 楼宇状态
 * @property string $province 省
 * @property string $city 市
 * @property string $area 区
 * @property string $address 详细地址
 * @property int $floor 楼层
 * @property string $business_circle 所在商圈
 * @property double $build_longitude 楼宇的经度
 * @property double $build_latitude 楼宇的纬度
 * @property string $contact_name 楼宇联系人名称
 * @property string $contact_tel 楼宇联系人电话
 * @property string $build_public_info 楼宇所需部分公共信息
 * @property string $build_special_info 楼宇所需特殊信息
 * @property string $build_appear_pic 楼宇外观照片（两张）
 * @property string $build_hall_pic 楼宇大厅照片（两张）
 * @property int $created_at 楼宇创建时间
 */
class BuildingRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'building_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['creator_id', 'created_at'], 'required'],
            [['creator_id', 'org_id', 'build_type_id', 'building_status', 'floor', 'created_at'], 'integer'],
            [['build_longitude', 'build_latitude'], 'number'],
            [['creator_name'], 'string', 'max' => 16],
            [['building_name'], 'string', 'max' => 30],
            [['province', 'city', 'area'], 'string', 'max' => 50],
            [['address'], 'string', 'max' => 100],
            [['contact_name', 'contact_tel'], 'string', 'max' => 32],
            [['build_public_info', 'build_special_info'], 'string', 'max' => 3000],
            [['build_exterior_pic', 'build_hall_pic'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                 => 'ID',
            'creator_id'         => 'Creator ID',
            'creator_name'       => 'Creator Name',
            'org_id'             => 'Org ID',
            'building_name'      => 'Building Name',
            'build_type_id'      => 'Build Type ID',
            'building_status'    => 'Building Status',
            'province'           => 'Province',
            'city'               => 'City',
            'area'               => 'Area',
            'address'            => 'Address',
            'floor'              => 'Floor',
            'business_circle'    => 'Business Circle',
            'build_longitude'    => 'Build Longitude',
            'build_latitude'     => 'Build Latitude',
            'contact_name'       => 'Contact Name',
            'contact_tel'        => 'Contact Tel',
            'build_public_info'  => 'Build Public Info',
            'build_special_info' => 'Build Special Info',
            'build_exterior_pic' => 'Build Exterior Pic',
            'build_hall_pic'     => 'Build Hall Pic',
            'created_at'         => 'Created At',
        ];
    }
    public static function saveBuildingRecord($recordParams)
    {
        return BuildingRecordApi::saveBuildingRecord($recordParams);
    }
    /**
     * 获取企业微信上传的图片
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-19
     * @param array $madiaId 素材ID
     * @return    [string]              [图片路径url]
     */
    private static function getWxUploadImg($mediaId)
    {
        $mediaImg = new MediaImg();
        return $mediaImg->getMediaImg($mediaId);
    }
    /**
     * 获取企业微信端提交的图片url
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-19
     * @param     [array]     $urlArr  [图片数组标识]
     * @return    [array]             [一组url]
     */
    public static function uploadRecordImg($urlArr)
    {
        $url = [];
        foreach ($urlArr as $key => $mediaID) {
            if (!$mediaID) {
                continue;
            }
            $imgName = time() . $key . '.jpg';
            $date    = date('Y-m-d');
            $imgUrl  = '/web/uploads/point/';
            if (!file_exists(Yii::$app->basePath . $imgUrl . $date)) {
                mkdir(Yii::$app->basePath . $imgUrl . $date, 0777, true);
            }
            $uploadImg = self::getWxUploadImg($mediaID);
            file_put_contents(Yii::$app->basePath . $imgUrl . $date . '/' . $imgName, $uploadImg);
            $imgFilePath = 'uploads/point/' . $date . '/' . $imgName;
            // 生成URL
            $url[$key] = Yii::$app->params['frontend'] . $imgFilePath;
        }
        return $url;
    }
    /**
     * 创建楼宇所需要的参数(企业微信)
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-13
     * @param     [str]     $userid [当前登录用户ID]
     * @return    [str]             [参数列表]
     */
    public static function getCreateBuildingRecord($userid)
    {
        $userInfo      = self::getUserRoleAndOrg($userid);
        $buildTypeList = self::getBuildTypeList();
        $role          = self::getRoleNameList($userInfo);
        if (in_array('楼宇修改', $role) || in_array('楼宇创建', $role)) {
            $buildingRecordInfo                          = [];
            $buildingRecordInfo['error_code']            = 0;
            $buildingRecordInfo['msg']                   = 'success';
            $buildingRecordInfo['data']['buildTypeList'] = $buildTypeList;
            $buildingRecordInfo['data']['org_id']        = $userInfo['branch'];
            $buildingRecordInfo['data']['user_id']       = $userid;
            $buildingRecordInfo['data']['role']          = true;
        } else {
            $buildingRecordInfo['error_code']   = 1;
            $buildingRecordInfo['msg']          = '当前登录用户无创建权限.';
            $buildingRecordInfo['data']['role'] = false;
        }
        return Json::encode($buildingRecordInfo);
    }

    /**
     * 获取楼宇类型列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-13
     * @return    [type]     [description]
     */
    private static function getBuildTypeList()
    {
        return BuildingRecordApi::getBuildTypeList();
    }
    /**
     * 获取自己创建的楼宇列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-15
     * @param     [string]     $userid [企业用户ID]
     * @return    [json]             [楼宇列表]
     */
    public static function getBuildingRecordList($userid, $orgID)
    {
        return BuildingRecordApi::getBuildingRecordList($userid, $orgID);
    }
    /**
     * 获取当前登录用户的权限列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-13
     * @param     [string]     $userid [用户]
     * @return    [array]             [权限列表]
     */
    private static function getRoleList($userid)
    {
        return Manager::getManagerRoleList($userid);
    }
    /**
     * 获取企业微信端登录的用户信息
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-15
     * @param     [string]     $userID [企业微信用户ID]
     * @return    [array]             [用户所在分公司和角色名称]
     */
    public static function getWxMemberInfo($userID)
    {
        $userInfo = WxMember::find()->select('position,org_id')->where(['userid' => $userID])->asArray()->one();
        if (!empty($userInfo['position'])) {
            $position = WxMember::$position[$userInfo['position']];
        }
        return ['position' => $position, 'ord_id' => $userInfo['org_id']];
    }
    /**
     * 获取用户所在分公司的ID和角色名称
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-16
     * @param     [string]     $userID [用户登录的ID]
     * @return    [array]             [角色名称和分公司ID]
     */
    public static function getUserRoleAndOrg($userID)
    {
        return Manager::getManagerRoleAndOrg($userID);
    }
    /**
     * 获取当前登录用户的分公司ID
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-13
     * @param     [string]     $userid [用户]
     * @return    [int]             [分公司ID]
     */
    public static function getOrgID($userid)
    {
        return WxMember::getOrgId($userid);
    }
    /**
     * 根据角色名称获取角色拥有的权限
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-13
     * @param     [str]     $roleName [角色名称]
     * @return    [array]               [角色列表]
     */
    public static function getRoleNameList($roleName)
    {
        return AuthItemChild::getRoleNameList($roleName);
    }
    /**
     * 获取需要更新的楼宇信息
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-20
     * @param     [int]     $id [楼宇记录ID]
     * @return    [array]         [楼宇信息]
     */
    public static function updateBuildingRecordInfo($id)
    {
        $buildingRecordList = BuildingRecordApi::updateBuildingRecordInfo($id);
        // 先判断进入角色的权限
        if (Yii::$app->user->can('楼宇修改')) {
            if ($buildingRecordList['data']['is_update'] == 1) {
                // 判断进入角色如果是BD就要判断是不是自己创建的
                if (Yii::$app->user->identity->role == 'BD') {
                    if ($buildingRecordList['data']['creatorID'] == Yii::$app->user->identity->userid) {
                        $buildingRecordList['data']['is_update'] = 1;
                    } else {
                        $buildingRecordList['data']['is_update'] = 0;
                    }
                } else {
                    $buildingRecordList['data']['is_update'] = 1;
                }
            }
        } else {
            $buildingRecordList['data']['is_update'] = 0;
        }
        return $buildingRecordList;
    }
    /**
     * 企业微信端修改楼宇信息接口 （因为权限判断问题）
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-20
     * @param     [int]     $id [楼宇记录ID]
     * @return    [array]         [楼宇信息]
     */
    public static function weChatUpdateRecord($id, $userid)
    {
        $buildingRecordList = BuildingRecordApi::updateBuildingRecordInfo($id);
        $userInfo           = self::getUserRoleAndOrg($userid);
        $role               = self::getRoleNameList($userInfo);
        if (in_array('楼宇修改', $role) || in_array('楼宇创建', $role)) {
            // 先判断传入的信息能不能修改
            if ($buildingRecordList['data']['is_update'] == 1) {
                // 判断进入角色如果是BD就要判断是不是自己创建的
                if ($role != 'BD') {
                    if ($buildingRecordList['data']['creatorID'] == $userid) {
                        $buildingRecordList['data']['is_update'] = 1;
                    } else {
                        $buildingRecordList['data']['is_update'] = 0;
                    }
                } else {
                    $buildingRecordList['data']['is_update'] = 1;
                }
            }
        } else {
            $buildingRecordList['data']['is_update'] = 0;
        }
        return $buildingRecordList;
    }
    /**
     * 获取查看详情
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-22
     * @param     [int]     $id [记录ID]
     * @return    [json]         [description]
     */
    public static function getBuildingRecordInfo($id)
    {
        return BuildingRecordApi::getBuildingRecordInfo($id);
    }
    /**
     * web端初始化列表接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-11-27
     * @param     integer    $orgID [登陆人的分公司ID]
     * @return    [array]            [楼宇列表]
     */
    public static function webGetBuildingRecordList($orgID = '')
    {
        $buildingList                       = BuildingRecordApi::webGetBuildingRecordList($orgID);
        $buildingList                       = Json::decode($buildingList);
        $buildingList['data']['newCreator'] = self::findCreatorNameList($orgID);
        $buildingList['data']['update']     = false;
        $buildingList['data']['create']     = false;
        $buildingList['data']['evaluate']   = false;
        $buildingList['data']['transmit']   = false;

        if (Yii::$app->user->can('楼宇修改')) {
            $buildingList['data']['update'] = true;
        }
        if (Yii::$app->user->can('楼宇初评')) {
            $buildingList['data']['evaluate'] = true;
        }
        if (Yii::$app->user->can('楼宇转交')) {
            $buildingList['data']['transmit'] = true;
        }
        if (Yii::$app->user->can('楼宇创建')) {
            $buildingList['data']['create'] = true;
        }
        if (Yii::$app->user->can('楼宇详情查看')) {
            $buildingList['data']['view'] = true;
        }
        return Json::encode($buildingList);
    }
    /**
     * 获取所有的BD人员
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-06
     * @param     [int]     $orgID  [分公司ID]
     * @return    [array]            [BD人员列表]
     */
    public static function findCreatorNameList($orgID)
    {
        return Manager::getManagerBdListByOrg($orgID);
    }
}
