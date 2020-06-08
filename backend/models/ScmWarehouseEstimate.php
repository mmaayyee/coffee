<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "scm_warehouse_estimate".
 *
 * @property string $id 预估单ID
 * @property string $author 领料人
 * @property string $warehouse_id 库信息ID
 * @property string $material_id 物料ID
 * @property string $material_out_num 领取物料的数量
 * @property int $status 状态 1-未发送 2-待配货 3-配货完成
 * @property string $date 领料日期
 * @property string $time 创建时间
 * @property string $material_type_id 物料分类id
 */
class ScmWarehouseEstimate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scm_warehouse_estimate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['warehouse_id', 'material_id', 'material_out_num', 'status', 'material_type_id'], 'integer'],
            [['confirm_date'], 'string'],
            [['date'], 'safe'],
            [['author'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'               => 'ID',
            'author'           => 'Author',
            'warehouse_id'     => 'Warehouse ID',
            'material_id'      => 'Material ID',
            'material_out_num' => 'Material Out Num',
            'status'           => 'Status',
            'date'             => 'Date',
            'time'             => 'Time',
            'material_type_id' => 'Material Type ID',
        ];
    }

    /**
     * 保存预估单
     * @author wangxiwen
     * @version 2018-10-12
     * @param array $estimateList 预估单
     * @return boolean
     */
    public static function saveWarehouseEstimate($estimateList)
    {
        $insertData = [];
        foreach ($estimateList as $estimateArr) {
            foreach ($estimateArr as $estimate) {
                if (!$estimate['material_out_num'] || !$estimate['author']) {
                    continue;
                }
                $insertData[] = [$estimate['author'], $estimate['warehouse_id'], $estimate['material_id'], $estimate['material_out_num'], $estimate['status'], $estimate['date'], $estimate['confirm_date'], $estimate['material_type_id'],
                ];
            }
        }
        if (!empty($insertData)) {
            $insertKey = ['author', 'warehouse_id', 'material_id', 'material_out_num', 'status', 'date', 'confirm_date', 'material_type_id'];
            return Yii::$app->db->createCommand()->batchInsert(self::tableName(), $insertKey, $insertData)->execute();
        }
        return true;
    }

    /**
     * 更新预估单分表
     * @author wangxiwen
     * @version 2018-11-14
     * @param string $date 日期
     * @param array $materialInfo 提交的物料信息
     * @return boolean
     */
    public static function saveEstimate($date, $materialInfo)
    {
        foreach ($materialInfo as $userId => $materialArray) {
            foreach ($materialArray as $materialTypeId => $packets) {
                $model = self::getEstimate($date, $userId, $materialTypeId);
                if (!$model) {
                    continue;
                }
                $model->material_out_num = $packets;
                $result                  = $model->save();
                if (!$result) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 获取预估单分表数据
     * @author wangxiwen
     * @version 2018-11-14
     * @param string $date 日期
     * @param string $userId 人员ID
     * @param int $materialTypeId 物料分类ID
     * @return object
     */
    public static function getEstimate($date, $userId, $materialTypeId)
    {
        return self::find()->where(['author' => $userId, 'material_type_id' => $materialTypeId, 'date' => $date])->one();
    }

}
