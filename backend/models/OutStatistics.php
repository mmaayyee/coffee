<?php

namespace backend\models;

use backend\models\ScmMaterial;
use common\models\WxMember;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "out_statistics".
 *
 * @property int $id ID
 * @property int $org_id 分公司ID
 * @property string $material_info 加料量数据
 * @property int $status 1待确认2正在出库3出库完成4审核完成
 * @property string $date 日期
 * @property int $type 1参考出库单2真实出库单
 */
class OutStatistics extends \yii\db\ActiveRecord
{
    public $startTime;
    public $endTime;

    // 待确认
    const NO_CONFIRM = 1;
    // 正在出库
    const OUTTING = 2;
    // 出库完成
    const OUTTED = 3;
    // 审核成功
    const AUDIT_SUCCESS = 4;
    // 审核失败
    const AUDIT_FAILURE = 5;
    // 复审完成
    const RETRIAL_COMPLETION = 6;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'out_statistics';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['org_id'], 'required'],
            [['org_id', 'status', 'type'], 'integer'],
            [['date'], 'safe'],
            [['material_info'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'org_id'        => '机构名称',
            'material_info' => '物料详情',
            'status'        => '状态',
            'date'          => '时间',
            'startTime'     => '开始时间',
            'endTime'       => '结束时间',
        ];
    }

    /**出库状态*/
    public static $statusArray = [
        self::NO_CONFIRM         => '待确认',
        self::OUTTING            => '正在出库',
        self::OUTTED             => '出库完成',
        self::AUDIT_SUCCESS      => '审核成功',
        self::AUDIT_FAILURE      => '审核失败',
        self::RETRIAL_COMPLETION => '复审成功',
    ];
    /**
     * 获取出库单和预估单统计数据物料添加量的差值
     * @author wangxiwen
     * @version 2018-06-21
     * @param object $outMaterialArr 出库单物料信息
     * @param object $estimateMaterialArr 预估单物料信息
     * @return json|string
     */
    public static function getDiffMaterial($outMaterialArr, $estimateMaterialArr)
    {
        $diffMaterials = [];
        foreach ($outMaterialArr as $materialTypeId => $outMaterialStr) {
            $outMaterialInfo = explode('|', $outMaterialStr);
            $outWeight       = $outMaterialInfo[0];
            $outPackets      = $outMaterialInfo[1];
            if (!empty($estimateMaterialArr[$materialTypeId])) {
                $estimateMaterialStr  = $estimateMaterialArr[$materialTypeId];
                $estimateMaterialInfo = explode('|', $estimateMaterialStr);
                $estimatePackets      = $estimateMaterialInfo[1];
            } else {
                $estimatePackets = 0;
            }
            $diffPackets = $outPackets - $estimatePackets;
            if ($diffPackets != 0) {
                $diffMaterials[$materialTypeId] = $outWeight . '|' . $diffPackets;
            }
        }
        return Json::encode($diffMaterials);
    }

    /**
     * 获取按人员和日期分组的出库单数据(scm_warehouse_out)
     * @author wangxiwen
     * @version 2018-06-20
     * @param $date  string 日期 格式yyyy-mm-dd
     * @param $orgId int 分公司ID
     * @return array
     */
    public static function getScmWarehouseOut($date, $orgId)
    {
        //查询分公司下的运维人员
        $userid = WxMember::getMemberIDArr($orgId);
        //获取物料规格
        $scmMaterial = ScmMaterial::getScmMaterial();
        //查询出库单数据
        return self::getOut($date, $userid, $scmMaterial);
    }
    /**
     * 获取运维出库单数据
     * @author wangxiwen
     * @version 2018-06-20
     * @param string $date  日期
     * @param array $userid 分公司下用户人员列表
     * @param array $scmMaterial 物料信息
     * @return array
     */
    private static function getOut($date, $userid, $scmMaterial)
    {
        $materialArr = ScmWarehouseOut::find()
            ->andWhere(['author' => $userid])
            ->andWhere(['date' => $date])
            ->asArray()
            ->all();
        $materialList = [];
        foreach ($materialArr as $material) {
            $typeId  = $material['material_type_id'];
            $author  = $material['author'];
            $packets = $material['material_out_num'];
            if (empty($materialList[$author][$typeId])) {
                continue;
            }
            $materialList[$author][$typeId]['packets']            = $packets;
            $materialList[$author][$typeId]['material_type_name'] = $scmMaterial[$typeId]['material_type_name'];
            $materialList[$author][$typeId]['spec_unit']          = $scmMaterial[$typeId]['spec_unit'];
            $materialList[$author][$typeId]['unit']               = $scmMaterial[$typeId]['unit'];
            $materialList[$author][$typeId]['weight']             = $scmMaterial[$typeId]['weight'];
        }
        return $materialList;
    }

    /**
     * 获取出库单分表数据中的运维人员和领料日期
     * @author wangxiwen
     * @version 2018-06-21
     * @param $orgId 分公司ID
     * @param $date 日期 格式 yyyy-mm-dd
     * @return array
     */
    public static function getOutAuthor($date, $orgId)
    {
        $userId    = WxMember::getMemberIDArr($orgId);
        $userArray = ScmWarehouseOut::find()
            ->distinct()
            ->alias('sw')
            ->leftJoin('wx_member wx', 'sw.author = wx.userid')
            ->andWhere(['sw.date' => $date])
            ->andWhere(['sw.author' => $userId])
            ->select('sw.author,sw.confirm_date,wx.name')
            ->asArray()
            ->all();
        $userList = [];
        foreach ($userArray as $userInfo) {
            $userList[$userInfo['author']]['name']         = $userInfo['name'];
            $userList[$userInfo['author']]['confirm_date'] = $userInfo['confirm_date'];
        }
        return $userList;
    }

    /**
     * 获取真实出库单material_info字段
     * @author wangxiwen
     * @version 2018-06-22
     * @param $userArr 运维人员
     * @return json|string
     */
    public static function getRealOutStatistics($userArr, $scmMaterial)
    {
        $warehouseOutList = ScmWarehouseOut::getRealWarehouseOut($userArr);
        $outStatistics    = [];
        foreach ($warehouseOutList as $warehouseOut) {
            if (!empty($outStatistics[$warehouseOut->material_type_id])) {
                $outStatistics[$warehouseOut->material_type_id] += $warehouseOut->material_out_num;
            } else {
                $outStatistics[$warehouseOut->material_type_id] = $warehouseOut->material_out_num;
            }
        }
        //物料Json数据
        $materialInfo = [];
        foreach ($outStatistics as $materialTypeId => $packets) {
            $weight                        = $scmMaterial[$materialTypeId]['weight'] ?? 0;
            $materialInfo[$materialTypeId] = $weight . '|' . $packets;
        }
        return Json::encode($materialInfo);
    }

    /**
     * 更新出库单统计数据状态
     * @author wangxiwen
     * @version 2018-10-10
     * @param int $status 状态
     * @param int $orgId 分公司ID
     * @return boolean
     */
    public static function saveStatusOutStatistics($status, $orgId)
    {
        return self::updateAll(
            ['status' => $status],
            ['org_id' => $orgId, 'date' => date('Y-m-d'), 'type' => 1]
        );
    }

    /**
     * 生成真实出库单
     * @author wangxiwen
     * @version 2018-10-10
     * @param int $orgId 分公司ID
     * @param string $materialInfo 真实出库单统计数据
     * @return boolean
     */
    public static function saveRealOutStatistics($orgId, $materialInfo)
    {
        $outStatistics                = new self();
        $outStatistics->org_id        = $orgId;
        $outStatistics->material_info = $materialInfo;
        $outStatistics->status        = self::OUTTED;
        $outStatistics->date          = date('Y-m-d');
        $outStatistics->type          = 2;
        //保存真实出库单统计数据
        return $outStatistics->save();
    }

    /**
     * 保存出库单统计
     * @author wangxiwen
     * @version 2018-10-12
     * @param  array $outStaticList 出库单统计数据
     */
    public static function saveWarehouseOutStatic($outStaticList)
    {
        foreach ($outStaticList as $static) {
            $outStatic                = new self();
            $outStatic->org_id        = $static['org_id'];
            $outStatic->material_info = $static['material_info'];
            $outStatic->status        = $static['status'];
            $outStatic->date          = $static['date'];
            $outStatic->type          = $static['type'];
            $outStaticRes             = $outStatic->save();
            if (!$outStaticRes) {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取出库单和预估单统计数据详情
     * @author wangxiwen
     * @version 2018-11-08
     * @param array $materialList 物料信息
     * @param array $scmMaterial 物料规格信息
     * @return
     */
    public static function getStatisticsDetail($materialList, $scmMaterial)
    {
        $content = '';
        foreach ($materialList as $materialTypeId => $materialStr) {
            $materialDetail = $scmMaterial[$materialTypeId] ?? '';
            if (!$materialDetail) {
                continue;
            }
            $type             = $materialDetail['type']; //是否放入料仓中的物料 1是2否
            $materialName     = $materialDetail['material_name'];
            $materialTypeName = $materialDetail['material_type_name'];
            $specUnit         = $materialDetail['spec_unit'];
            $unit             = $materialDetail['unit'];
            $materialInfo     = explode('|', $materialStr);
            $weight           = $materialInfo[0];
            $packets          = $materialInfo[1];
            if ($type == 1) {
                $showSpecUnit = '-' . $weight . $specUnit;
            } else {
                $showSpecUnit = $weight > 1 ? '-' . $weight . $specUnit : '';
            }
            $content .= '<tr><td>' . $materialTypeName . '</td><td>' . $materialName . $showSpecUnit . '</td><td>' . $packets . $unit . '</td></tr>';
        }
        return $content;
    }

    /**
     * 获取出库单和预估单统计数据差异值
     * @author wangxiwen
     * @version 2018-11-08
     * @param array $diffMaterialArr 出库单预估单物料数据(差异值)
     * @param array $scmMaterial 物料规格信息
     * @return
     */
    public static function getDiffStatisticsDetail($diffMaterialArr, $scmMaterial)
    {
        $content = '';
        foreach ($diffMaterialArr as $materialTypeId => $materialStr) {
            $materialDetail = $scmMaterial[$materialTypeId] ?? '';
            if (!$materialDetail) {
                continue;
            }
            $type             = $materialDetail['type']; //是否放入料仓中的物料 1是2否
            $materialName     = $materialDetail['material_name'];
            $materialTypeName = $materialDetail['material_type_name'];
            $specUnit         = $materialDetail['spec_unit'];
            $unit             = $materialDetail['unit'];
            $materialInfo     = explode('|', $materialStr);
            $weight           = $materialInfo[0];
            $packets          = $materialInfo[1];
            $class            = $packets > 0 ? 'glyphicon glyphicon-arrow-up' : 'glyphicon glyphicon-arrow-down';
            if ($type == 1) {
                $showSpecUnit = '-' . $weight . $specUnit;
            } else {
                $showSpecUnit = $weight > 1 ? '-' . $weight . $specUnit : '';
            }
            $content .= '<tr><td>' . $materialTypeName . '</td><td>' . $materialName . $showSpecUnit . '</td><td>' . abs($packets) . $unit . '<b class="' . $class . '"></b></td></tr>';
        }
        return $content;
    }

    /**
     * 获取运维人员领料详情
     * @author wangxiwen
     * @version 2018-11-08
     * @param array $outMaterialArray 出库单分表数据
     * @param array $estimateMaterialArray 预估单分表数据
     * @param array $outAuthorArray 出库单人员
     * @param string
     */
    public static function getCollectMaterialDetail($outMaterialArray, $estimateMaterialArray, $outAuthorArray)
    {
        $content = '';
        foreach ($outMaterialArray as $userid => $outMaterial) {
            $content .= '<tr><td>' . $outAuthorArray[$userid]['name'] . '</td>';
            $showOutData      = '<td>';
            $showEstimateData = '<td>';
            $diffData         = '<td>';
            foreach ($outMaterial as $materialTypeId => $material) {
                $materialTypeName = $material['material_type_name'];
                $specUnit         = $material['spec_unit'];
                $unit             = $material['unit'];
                $weight           = $material['weight'];
                $packets          = $material['packets'];
                //预估单物料基础数据
                $estimatePakets = $estimateMaterialArray[$userid][$materialTypeId]['packets'] ?? 0;
                //预估单和出库单的物料规格weight有可能会不同
                $estimateWeight = $estimateMaterialArray[$userid][$materialTypeId]['weight'] ?? $weight;
                //出库单和预估单的物料规格不一致,以出库单物料规格为准
                if ($weight != $estimateWeight) {
                    $diffPackets = ceil(($packets * $weight - $estimatePakets * $estimatePakets) / $weight);
                } else {
                    $diffPackets = $packets - $estimatePakets;
                }
                $class = $diffPackets > 0 ? 'glyphicon glyphicon-arrow-up' : 'glyphicon glyphicon-arrow-down';
                //差值列展示数据
                if ($diffPackets != 0) {
                    $diffData .= $materialTypeName . '-' . $weight . $specUnit . ' * ' . abs($diffPackets) . $unit . '<b class="' . $class . '"></b><br/>';
                }
                if ($estimatePakets > 0) {
                    $showEstimateData .= $materialTypeName . '-' . $estimateWeight . $specUnit . ' * ' . $estimatePakets . $unit . '<br/>';
                }
                $showOutData .= $materialTypeName . '-' . $weight . $specUnit . ' * ' . $packets . $unit . '<br/>';
            }
            $content .= $showEstimateData . '</td>';
            $content .= $showOutData . '</td>';
            $content .= $diffData . '</td>';
            $content .= '<td>' . $outAuthorArray[$userid]['confirm_date'] . '</td>';
            $content .= '</tr>';
        }
        return $content;
    }

    /**
     * 复审出库单时展示物料详情
     * @author wangxiwen
     * @version 2018-11-08
     * @param array $outMaterialArray 出库单物料详情
     * @param array $scmMaterial 物料规格信息
     * @return
     */
    public static function getExamineMaterialDetail($outMaterialArray, $scmMaterial)
    {
        $content = '';
        foreach ($outMaterialArray as $materialTypeId => $materialStr) {
            $materialDetail = $scmMaterial[$materialTypeId] ?? '';
            if (!$materialDetail) {
                continue;
            }
            $type             = $materialDetail['type']; //是否放入料仓中的物料 1是2否
            $materialTypeName = $materialDetail['material_type_name'];
            $specUnit         = $materialDetail['spec_unit'];
            $materialInfo     = explode('|', $materialStr);
            $weight           = $materialInfo[0];
            $packets          = $materialInfo[1];
            $class            = $packets > 0 ? 'glyphicon glyphicon-arrow-up' : 'glyphicon glyphicon-arrow-down';
            if ($type == 1) {
                $showSpecUnit = $materialTypeName . '-' . $weight . $specUnit;
            } else {
                $showSpecUnit = $weight > 1 ? $materialTypeName . '-' . $weight . $specUnit : $materialTypeName;
            }
            $content .= '<label class="control-label">' . $showSpecUnit . '</label><br/><input type="text" class="form-control" size="100" name="material_info[' . $materialTypeId . ']" value="' . $packets . '"><br/>';
        }
        return $content;
    }

    /**
     * 更新出库单复审物料数量
     * @author wangxiwen
     * @version 2018-11-08
     * @param object $model 出库单信息
     * @param array $materialArray 修改前的物料信息
     * @param array $newMaterialArray 新提交的物料信息
     * @return
     */
    public static function saveExamineMaterialNumber($model, $materialArray, $newMaterialArray)
    {
        $newMaterialInfo = self::filterMaterialPackets($newMaterialArray);
        //更新复审后的出库单物料数量
        $materialInfo = [];
        foreach ($materialArray as $materialTypeId => $materialStr) {
            $packets = $newMaterialInfo[$materialTypeId] ?? 0;
            if (!$packets) {
                continue;
            }
            $materialDetail                = explode('|', $materialStr);
            $weight                        = $materialDetail[0];
            $materialInfo[$materialTypeId] = $weight . '|' . $packets;
        }
        $model->material_info = Json::encode($materialInfo);
        $model->status        = self::RETRIAL_COMPLETION;
        $result               = $model->save();
        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * 获取出库单列表页物料详情展示
     * @author wangxiwen
     * @version 2018-11-08
     * @param array $materialArray 出库物料信息
     * @param array $scmMaterial 物料规格信息
     * @return
     */
    public static function getMaterialDetail($materialArray, $scmMaterial)
    {
        $showMaterial = '';
        foreach ($materialArray as $materialTypeId => $materialStr) {
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
            if ($type == 1) {
                $showSpecUnit = '-' . $weight . $specUnit;
            } else {
                $showSpecUnit = $weight > 1 ? '-' . $weight . $specUnit : '';
            }
            $showMaterial .= $materialTypeName . $showSpecUnit . ' * ' . $packets . $unit . '<br/>';
        }
        return $showMaterial;
    }

    /**
     * 过滤物料添加量数值
     * @author wangxiwen
     * @version 2018-06-20
     * @return string|json
     */
    public static function filterMaterialPackets($materialPacketList)
    {
        $materialPackets = [];
        foreach ($materialPacketList as $materialTypeId => $packets) {
            if (is_numeric($packets) && $packets >= 0) {
                $materialPackets[$materialTypeId] = $packets;
            } else {
                $materialPackets[$materialTypeId] = 0;
            }
        }
        return $materialPackets;
    }
}
