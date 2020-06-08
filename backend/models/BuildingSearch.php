<?php

namespace backend\models;

use backend\models\BuildType;
use backend\models\Organization;
use common\helpers\Tools;
use common\models\Api;
use common\models\Building;
use common\models\BuildingApi;
use common\models\EquipmentsChild;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * BuildingSearch represents the model behind the search form about `common\models\Building`.
 */
class BuildingSearch extends Building
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'people_num', 'create_time', 'build_type', 'build_status', 'org_id'], 'integer'],
            [['name', 'address', 'contact_name', 'contact_tel', 'code', 'province', 'city', 'area', 'build_number', 'bd_maintenance_user', 'sign_org_id', 'source_org_id', 'business_type'], 'safe'],
            [['longitude', 'latitude', 'is_share', 'is_delivery', 'building_level'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $this->orgArr = Api::getOrgIdNameArray();
        $query        = Building::find()->orderBy('id DESC');
        $query->orderby('id desc');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        $query->andFilterWhere([
            'id'                  => $this->id,
            'people_num'          => $this->people_num,
            'business_type'       => $this->business_type,
            'create_time'         => $this->create_time,
            'longitude'           => $this->longitude,
            'latitude'            => $this->latitude,
            'build_type'          => $this->build_type,
            'build_status'        => $this->build_status,
            'is_share'            => $this->is_share,
            'is_delivery'         => $this->is_delivery,
            'building_level'      => $this->building_level,
            'bd_maintenance_user' => trim($this->bd_maintenance_user),
        ]);
        if ($this->org_id) {
            $orgIdArr = Api::getOrgIdArray(['parent_path' => $this->org_id]);
            $query->andFilterWhere(['org_id' => $orgIdArr]);
        }
        if ($this->sign_org_id) {
            $orgIdArr = Api::getOrgIdArray(['parent_path' => $this->sign_org_id]);
            $query->andFilterWhere(['sign_org_id' => $orgIdArr]);
        }
        if ($this->source_org_id) {
            $orgIdArr = Api::getOrgIdArray(['parent_path' => $this->source_org_id]);
            $query->andFilterWhere(['source_org_id' => $orgIdArr]);
        }
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'contact_name', $this->contact_name])
            ->andFilterWhere(['like', 'contact_tel', $this->contact_tel])
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'build_number', $this->build_number])
            ->andFilterWhere(['like', 'area', $this->area]);
        if (!empty($params['export'])) {
            return $query->asArray()->all();
        }
        return $dataProvider;
    }

    /**
     *  代理商调用erp数据
     * @author   sulingling
     * @dateTime 2018-08-30
     * @version  [version]
     * @param    array()     $params [搜索条件]
     * @return   array()             [返回的详细信息]
     */
    public static function searchAgentsBuild($params)
    {
        $page   = isset($params['page']) ? $params['page'] : 0;
        $orgArr = Api::getOrgIdNameArray();
        $query  = Building::find();
        $query->orderby('id desc');

        if (!empty($params['BuildingSearch']['build_name'])) {
            $query->andFilterWhere(['like', 'name', $params['BuildingSearch']['build_name']]);
        }
        if (!empty($params['BuildingSearch']['build_status'])) {
            $query->andFilterWhere(['build_status' => $params['BuildingSearch']['build_status']]);
        }
        if (!empty($params['BuildingSearch']['agents_id'])) {
            $query->andFilterWhere(['org_id' => $params['BuildingSearch']['agents_id']]);
        }
        if (!empty($params['BuildingSearch']['equip_code'])) {
            $query->andFilterWhere(['build_number' => $params['BuildingSearch']['equip_code']]);
        }
        $count = $query->count();
        $query->limit(20);
        $query->offset($page);
        // echo $query->createCommand()->getRawSql();die;
        return ['buildingInfo' => $query->all(), 'total' => $count];
    }
    /**
     * 点位导出
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-04
     * @param     [type]     $params [description]
     * @return    [type]             [description]
     */
    public function exportSearch($params)
    {
        // $this->load($params);
        // $buildingList = BuildingApi::exportBuildingList($params);
        $params['export'] = 1;
        $buildingList     = $this->search($params);
        $buildTypeList    = BuildType::getBuildType();
        $orgList          = Organization::getOrgList();
        $orgIdNameList    = ArrayHelper::map($orgList, 'org_id', 'org_name');
        $orgIdPidList     = ArrayHelper::map($orgList, 'org_id', 'parent_id');
        $equipList        = EquipmentsChild::getPutInEquipList();
        $bidEquipCodeList = EquipmentsChild::getBuildIdEquipCodeList($equipList);
        $bidOperaTimeList = EquipmentsChild::getBidOperaTimeList($equipList);
        $couponGroupList  = Api::getCouponGroup();
        $buildNumTaskList = BuildingApi::getBuildNumTaskList();
        $buildStatusList  = Building::$build_status;
        unset($buildStatusList['']);
        $contactList = self::findContactList();
        $title       = "点位信息列表";
        $header      = ['点位名称', '点位编号', '渠道类型', '所属机构', '上级机构', 'BD维护人员', '设备编号', '点位状态', '点位级别', '联系人姓名', '联系人号码', '省份', '城市', '区域', '详细地址', '首杯免费策略', '首杯策略变更日期', '首杯备份策略', '是否支持外送', '是否有任务支付', '点位人数', '开始运营时间', '合同签约公司', '客户来源', '业务类型', '能否被搜索'];
        $dataList    = [];
        foreach ($buildingList as $building) {
            $contact = $contactList[$building['build_number']] ?? [];
            if ($contact) {
                $contactName = $contact['contact_name'] ?? '';
                $contactTel  = $contact['contact_tel'] ?? '';
                $peopleNum   = $contact['people_num'] ?? '';
            }
            $parentId   = $orgIdPidList[$building['org_id']] ?? 0;
            $dataList[] = [
                $building['name'],
                $building['build_number'],
                $buildTypeList[$building['build_type']] ?? '',
                $orgIdNameList[$building['org_id']] ?? '',
                $orgIdNameList[$parentId] ?? '',
                $building['bd_maintenance_user'],
                $bidEquipCodeList[$building['id']] ?? '',
                $buildStatusList[$building['build_status']] ?? '',
                Building::getBuildLevel($building['building_level']),
                $contactName,
                $contactTel,
                $building['province'],
                $building['city'],
                $building['area'],
                $building['address'],
                $couponGroupList[$building['first_free_strategy']] ?? '',
                $building['strategy_change_date'],
                $couponGroupList[$building['first_backup_strategy']] ?? '',
                $building['is_delivery'] == 1 ? '支持' : '不支持',
                $buildNumTaskList[$building['build_number']] ?? '',
                $peopleNum,
                $bidOperaTimeList[$building['id']] ?? '',
                $orgIdNameList[$building['sign_org_id']] ?? '',
                $orgIdNameList[$building['source_org_id']] ?? '',
                Building::$businessTypeList[$building['business_type']] ?? '',
                $building['is_share'] == 1 ? '是' : '否',
            ];
        }
        Tools::exportData($title, $header, $dataList);
    }
    /**
     * 获取所有的楼宇人数和楼宇联系人
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-06
     * @return    [array ]     [楼宇联系人]
     */
    private static function findContactList()
    {
        $list = self::find()
            ->select('build_number,contact_name,contact_tel,people_num')
            ->asArray()
            ->all();
        $contactList = [];
        foreach ($list as $key => $contact) {
            $contactList[$contact['build_number']] = $contact;
        }
        return $contactList;
    }
}
