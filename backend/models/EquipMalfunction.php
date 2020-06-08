<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "equip_malfunction".
 *
 * @property string $id
 * @property string $content
 * @property string $ctime
 *
 * @property Task[] $tasks
 */
class EquipMalfunction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_malfunction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'required'],
            [['content'], 'unique'],
            [['is_del'], 'integer'],
            [['content'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => '故障原因',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::className(), ['malfunction_id' => 'id']);
    }
    /**
     * 获取故障原因列表
     * @return [type] [description]
     */
    public static function getMalfunctionReasonList()
    {
       $list =  self::find()->select('id,content')->where(['!=' , 'is_del', '2'])->asArray()->all();
       $malfunctionReasonList = [];
       foreach ($list as $key => $value) {
           $malfunctionReasonList[$value['id']] = $value['content'];
       }
       return $malfunctionReasonList;
    }
    /**
     * 根据故障原因id获取故障原因内容
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function getMalfunctionReasonName($id)
    {
        $id = explode(',', $id);
        $list = self::find()->select('content')->where(['id'=>$id])->asArray()->all();
        $str = '';
        foreach ($list as $k=>$v) {
            $str .= $v['content'].'，';
        }
        $str = trim($str,'，');
        return $str;
    }

    /**
     * 获取物料详细信息
     * @param  [type]
     * @param  [type]
     * @return [type]
     */
    public static function getMalfunctionDetail($filed,$where)
    {
        return self::find() -> select($filed) -> where($where) -> asArray() -> one();
    }
}
