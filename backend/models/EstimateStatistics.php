<?php

namespace backend\models;

use backend\models\ScmMaterial;
use backend\models\ScmWarehouseEstimate;
use common\models\WxMember;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "estimate_statistics".
 *
 * @property int $id ID
 * @property int $org_id 分公司ID
 * @property string $material_info 加料量数据
 * @property int $status 状态 1-未发送 2-待配货 3-配货完成
 * @property string $date 日期
 * @property int $type 1参考预估单2真实预估单
 */
class EstimateStatistics extends \yii\db\ActiveRecord
{
    public $startTime;
    public $endTime;

    const NO_SEND         = 1; //待发送
    const NO_DISTRIBUTION = 2; //待配货
    const DISTRIBUTED     = 3; //配货完成
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'estimate_statistics';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['org_id'], 'required'],
            [['org_id', 'status', 'type'], 'integer'],
            [['date', 'distribution_date', 'send_date'], 'safe'],
            [['material_info'], 'string', 'max' => 1000],
        ];
    }

    /**预估单状态*/
    public static $statusArray = [
        self::NO_SEND         => '未发送',
        self::NO_DISTRIBUTION => '待配货',
        self::DISTRIBUTED     => '配货完成',
    ];

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'org_id'            => '机构名称',
            'material_info'     => '物料详情',
            'status'            => '状态',
            'date'              => '时间',
            'send_date'         => '发送时间',
            'distribution_date' => '配货完成时间',
            'startTime'         => '开始时间',
            'endTime'           => '结束时间',
        ];
    }

    /**
     * 获取按人员和日期分组的预估单数据(scm_warehouse_estimate)
     * @author wangxiwen
     * @version 2018-06-20
     * @param $date  string 日期 格式yyyy-mm-dd
     * @param $orgId int 分公司ID
     * return array
     */
    public static function getScmWarehouseEstimate($date, $orgId)
    {
        //查询分公司下的运维人员
        $useridArr = WxMember::getMemberIDArr($orgId);
        //获取物料规格
        $scmMaterial = ScmMaterial::getScmMaterial();
        //查询预估单数据
        return self::getEstimate($date, $useridArr, $scmMaterial);
    }

    /**
     * 获取运维预估单展示数据
     * @author wangxiwen
     * @version 2018-06-20
     * @param $date  string 日期 格式yyyy-mm-dd
     * @param $useridArr 分公司下用户人员列表
     * @param $scmMaterial 物料信息
     * @return array
     */
    private static function getEstimate($date, $useridArr, $scmMaterial)
    {
        $materialArr = ScmWarehouseEstimate::find()
            ->andWhere(['in', 'author', $useridArr])
            ->andWhere(['date' => $date])
            ->asArray()
            ->all();
        $materialList = [];
        foreach ($materialArr as $material) {
            $materialTypeId = $material['material_type_id'];
            $userId         = $material['author'];
            $materialInfo   = $scmMaterial[$materialTypeId] ?? [];
            if (empty($materialInfo)) {
                continue;
            }
            $materialList[$userId][$materialTypeId]['material_type_name'] = $materialInfo['material_type_name'];
            $materialList[$userId][$materialTypeId]['spec_unit']          = $materialInfo['spec_unit'];
            $materialList[$userId][$materialTypeId]['unit']               = $materialInfo['unit'];
            $materialList[$userId][$materialTypeId]['weight']             = $materialInfo['weight'];
            $materialList[$userId][$materialTypeId]['packets']            = $material['material_out_num'];
        }
        return $materialList;
    }

    /**
     * 更新预估单统计表数据
     * @author wangxiwen
     * @version 2018-06-21
     * @param $model 预估单
     * @param $materialInfo 提交数据
     * @return array
     */
    public static function saveEstimateStatistics($model, $materialInfo)
    {
        $materialArray = $model && $model->material_info ? Json::decode($model->material_info) : [];
        //获取提交数据中所有物料总和
        $packetTotal = self::getMaterialPacketTotal($materialInfo);
        //将统计数据更新成提交数据
        $materialInfo = self::getMaterialInfo($materialArray, $packetTotal);
        //更新鱼尾预估单统计表数据
        $model->material_info = Json::encode($materialInfo);
        $model->status        = self::NO_DISTRIBUTION;
        $model->send_date     = date('Y-m-d');
        $result               = $model->save();
        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * 获取预估单提交物料总和
     * @author wangxiwen
     * @version 2018-11-14
     * @param array $materialInfo 表单提交预估单物料信息
     * @return
     */
    protected static function getMaterialPacketTotal($materialInfo)
    {
        $packetTotal = [];
        foreach ($materialInfo as $userid => $material) {
            //过滤物料数据
            $materials = OutStatistics::filterMaterialPackets($material);
            foreach ($materials as $materialTypeId => $packets) {
                if (!empty($packetTotal[$materialTypeId])) {
                    $packetTotal[$materialTypeId] += $packets;
                } else {
                    $packetTotal[$materialTypeId] = $packets;
                }
            }
        }
        return $packetTotal;
    }

    /**
     * 获取预估单提交物料总和
     * @author wangxiwen
     * @version 2018-11-14
     * @param array $materialArray 预估单统计原物料信息
     * @param array $packetTotal 新提交物料总和
     * @return
     */
    protected static function getMaterialInfo($materialArray, $packetTotal)
    {
        $overMaterial = [];
        foreach ($materialArray as $materialTypeId => $material) {
            $materialDetail = explode('|', $material);
            $weight         = $materialDetail[0];
            $packets        = $packetTotal[$materialTypeId] ?? 0;
            if ($packets <= 0) {
                continue;
            }
            $overMaterial[$materialTypeId] = $weight . '|' . $packets;
        }
        return $overMaterial;
    }

    /**
     * 获取预估单统计表数据
     * @author wangxiwen
     * @version 2018-06-21
     * @param int $orgId 分公司ID
     * @param string $date 日期
     * @return object
     */
    public static function getEstimateStatistic($orgId, $date)
    {
        return self::find()->where(['org_id' => $orgId, 'date' => $date])->one();
    }

    /**
     * 保存预估单统计
     * @author wangxiwen
     * @version 2018-10-12
     * @param  array $estimateStaticList 预估单统计数据
     */
    public static function saveWarehouseEstimateStatic($estimateStaticList)
    {
        foreach ($estimateStaticList as $static) {
            $estimateStatic                = new self();
            $estimateStatic->org_id        = $static['org_id'];
            $estimateStatic->material_info = $static['material_info'];
            $estimateStatic->status        = $static['status'];
            $estimateStatic->date          = $static['date'];
            $estimateStatic->type          = $static['type'];
            $estimateStaticRes             = $estimateStatic->save();
            if (!$estimateStaticRes) {
                return false;
            }
        }
        return true;
    }

    /**
     * 复审出库单时展示物料详情
     * @author wangxiwen
     * @version 2018-11-08
     * @param array $estimateMaterialArray 出库单物料详情
     * @param array $scmMaterial 物料规格信息
     * @return
     */
    public static function getEstimateMaterialDetail($estimateMaterialArray, $scmMaterial)
    {
        $content = '';
        foreach ($estimateMaterialArray as $materialTypeId => $materialStr) {
            $materialDetail = $scmMaterial[$materialTypeId] ?? '';
            if (!$materialDetail) {
                continue;
            }
            $type             = $materialDetail['type']; //是否放入料仓中的物料 1是2否
            $materialTypeName = $materialDetail['material_type_name'];
            $specUnit         = $materialDetail['spec_unit'];
            $unit             = $materialDetail['unit'];
            $materialInfo     = explode('|', $materialStr);
            $weight           = $materialInfo[0];
            $packets          = $materialInfo[1];
            if ($packets <= 0) {
                continue;
            }
            if ($type == 1) {
                $showSpecUnit = $materialTypeName . '-' . $weight . $specUnit;
            } else {
                $showSpecUnit = $weight > 1 ? $materialTypeName . '-' . $weight . $specUnit : $materialTypeName;
            }
            $content .= '<label class="control-label">' . $showSpecUnit . '*' . $packets . $unit . '</label><br/>';
        }
        return $content;
    }

    /**
     * 获取预估单修改数据
     * @author wangixwen
     * @version 2018-11-13
     * @param array $estimateData 预估单信息
     * @param array $userNameArray 用户信息
     * @param array $buildNameArray 楼宇信息
     * @return
     */
    public static function getEstimateShowData($estimateData, $userNameArray, $buildNameArray)
    {
        $content = '';
        foreach ($estimateData as $userid => $estimateArray) {
            $userName = $userNameArray[$userid] ?? '';
            $content .= '<tr><td><label class="control-label">' . $userName . '</label></td><td>';
            foreach ($estimateArray as $materialTypeId => $estimate) {
                $content .= '<label class="control-label">' . $estimate['material_type_name'] . '-' . $estimate['weight'] . $estimate['spec_unit'] . '</label><input type="text" class="form-control" size="10" style="margin-left:10px" name="material_info[' . $userid . '][' . $materialTypeId . ']" value="' . $estimate['packets'] . '">' . '<br/>';
            }
            $content .= '</td><td>';
            $buildNameArray = $buildName[$userid] ?? [];
            foreach ($buildNameArray as $build) {
                $content .= $build . '<br/>';
            }
            $content .= '</td></tr>';
        }
        return $content;
    }
}
