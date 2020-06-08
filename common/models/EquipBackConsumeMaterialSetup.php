<?php

namespace common\models;

use backend\modules\service\helpers\Api;
use common\models\ArrayDataProviderSelf;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "equip_back_consume_material_setup".
 *
 * @property int $setup_id 设置ID
 * @property string $config_key 参数名称
 * @property string $config_value 参数值
 * @property int $equip_type_id 设备类型ID
 * @property int $create_time 添加时间
 */
class EquipBackConsumeMaterialSetup extends \yii\db\ActiveRecord
{
    public $equip_type_id;
    public $config_key;
    public $config_value;
    public $create_time;
    public $setup_id;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_type_id', 'create_time', 'setup_id'], 'integer'],
            [['config_key', 'config_value'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'setup_id'      => '设置ID',
            'config_key'    => '参数名称',
            'config_value'  => '参数值',
            'equip_type_id' => '设备类型ID',
            'create_time'   => '添加时间',
        ];
    }

    /**
     * 保存物料消耗设置
     * @author zhenggangwei
     * @date   2020-03-19
     * @param  array     $data 需要保存的数据
     * @return boole
     */
    public static function saveSetup($data)
    {
        $res = Json::decode(Api::postBase('erpapi/equip-back-consume-material-setup/save-setup', $data));
        if ($res['error_code'] == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 保存物料消耗设置
     * @author zhenggangwei
     * @date   2020-03-19
     * @param  array     $data 需要保存的数据
     * @return boole
     */
    public static function getSetupInfo($id)
    {
        $res = Json::decode(Api::getBase('erpapi/equip-back-consume-material-setup/get-setup-info', '&id=' . $id));
        if ($res['error_code'] == 0) {
            $setup = new self();
            $setup->load(['EquipBackConsumeMaterialSetup' => $res['data']]);
            return $setup;
        } else {
            return [];
        }
    }

    /**
     * 删除物料消耗设置
     * @author zhenggangwei
     * @date   2020-03-19
     * @param  integer     $id 设置ID
     * @return array
     */
    public static function deleteSetup($id)
    {
        $res = Json::decode(Api::getBase('erpapi/equip-back-consume-material-setup/delete-setup', '&id=' . $id));
        if ($res['error_code'] == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 分页获取物料消耗设置数据
     * @author zhenggangwei
     * @date   2020-03-19
     * @param  array     $params 查询条件
     * @return array
     */
    public function getSetupList($params)
    {
        $this->load($params);
        $params['page'] = Yii::$app->request->get('page', 1);
        $setupList      = Json::decode(Api::postBase('erpapi/equip-back-consume-material-setup/get-setup-list', $params));
        if ($setupList['error_code'] != 0) {
            return [];
        }
        $dataProvider = [];
        if ($setupList) {
            foreach ($setupList['data']['setupList'] as $data) {
                $setup = new self();
                $setup->load(['EquipBackConsumeMaterialSetup' => $data]);
                $dataProvider[$data['setup_id']] = $setup;
            }
        }
        $setupData = new ArrayDataProviderSelf([
            'allModels'  => $dataProvider,
            'pagination' => [
                'pageSize' => 20,
            ],
            'totalCount' => $setupList['data']['total'] ?? 0,
            'sort'       => [
                'attributes' => ['setup_id desc'],
            ],
        ]);
        return $setupData;
    }

}
