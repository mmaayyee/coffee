<?php

namespace backend\models;

use backend\models\BuildingSiteStatistics;
use common\models\ArrayDataProviderSelf;
use common\models\EquipDeliveryRecord;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BuildingSiteStatisticsSearch represents the model behind the search form of `backend\models\BuildingSiteStatistics`.
 */
class BuildingSiteStatisticsSearch extends BuildingSiteStatistics
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['build_number', 'name', 'organization_type', 'org_name', 'org_city', 'create_time', 'un_bind_time', 'equipment_code', 'equipment_type_name', 'build_site_statistics'], 'safe'],
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
        $page           = isset($params['page']) ? $params['page'] : 1;
        $params         = !empty($params) ? $params['BuildingSiteStatisticsSearch'] : [];
        $params['page'] = $page;
        $list           = BuildingSiteStatistics::getBuildSiteShowData($params);
        $count          = empty($list['buildSiteCount']) ? 0 : $list['buildSiteCount'];
        //获取运营结束时间
        $deliveryRecordList = EquipDeliveryRecord::getBuildUntieTime();
        $dataProvider       = [];
        if (isset($list['buildSiteStatistics'])) {
            foreach ($list['buildSiteStatistics'] as $key => $buildSiteData) {
                $buildNumber                   = $buildSiteData['build_number'];
                $buildSiteData['un_bind_time'] = $deliveryRecordList[$buildNumber] ?? '';
                $buildSiteStatistics           = new BuildingSiteStatistics();
                $buildSiteStatistics->load(['BuildingSiteStatistics' => $buildSiteData]);
                $dataProvider[$key] = $buildSiteStatistics;
            }
        }
        $list = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20, //每页显示条数
            ],
            'totalCount' => $count,
        ]);
        return $list;
    }

    /**
     * 导出功能
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function exportSearch($params)
    {
        $params        = !empty($params) ? $params['BuildingSiteStatisticsSearch'] : [];
        $buildSiteList = BuildingSiteStatistics::getBuildSiteExportData($params);
        //获取运营结束时间,且将运营结束时间压入统计数据中
        $deliveryRecordList = EquipDeliveryRecord::getBuildUntieTime();

        foreach ($buildSiteList as $key => $buildSiteData) {
            $buildNumber                         = $buildSiteData['build_number'];
            $buildSiteList[$key]['un_bind_time'] = $deliveryRecordList[$buildNumber] ?? '';
        }
        return $buildSiteList;
    }
}
