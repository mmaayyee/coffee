<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "equip_extra_log".
 *
 * @property integer $id
 * @property integer $build_id
 * @property integer $equip_id
 * @property integer $equip_extra_id
 * @property integer $status
 * @property integer $create_user
 * @property integer $create_time
 */
class EquipExtraLog extends \yii\db\ActiveRecord
{
    //使用中
    const USING = 1;
    //被替换
    const REPLACED = 2;

    public static $status = [
        self::USING => '使用中',
        self::REPLACED => '被回收',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_extra_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['build_id', 'equip_id', 'equip_extra_id', 'status', 'create_time'], 'integer'],
            [['create_user'],'string','max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'build_id' => '楼宇名称',
            'equip_id' => '设备ID',
            'equip_extra_id' => '附件名称',
            'status' => '状态',
            'create_user' => '负责人',
            'create_time' => '附件添加时间',
        ];
    }

    /**
     * 插入附件记录
     * @param array $data
     * @return bool
     */
    public static function addExtraRecord($data = array())
    {
        $result = true;
        $status = self::USING;
        //修改正在使用的附件为被回收
        if ($data['process_result'] == 4) {
            $status = self::REPLACED;
        }
        //新增/回收附件
        $model = new EquipExtraLog();
        $model->equip_id = $data['equip_id'];
        $model->build_id = $data['build_id'];
        $model->equip_extra_id = $data['equip_extra_id'];
        $model->status = $status;
        $model->create_user = $data['create_user'];
        $model->create_time = $data['create_time'];
        $result = $model->save();

        if ($result) {
            return true;
        } else {
            return false;
        }

    }

}
