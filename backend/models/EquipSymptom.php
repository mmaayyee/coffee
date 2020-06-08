<?php

namespace backend\models;

use common\helpers\Tools;
use Yii;

/**
 * This is the model class for table "equip_symptom".
 *
 * @property integer $id
 * @property string $symptom
 */
class EquipSymptom extends \yii\db\ActiveRecord
{
    /** 删除状态常量定义 */
    // 未删除
    const DEL_NO = 1;
    // 已删除
    const DEL_YES = 2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_symptom';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['symptom'], 'required'],
            [['symptom'], 'unique'],
            [['is_del'], 'integer'],
            [['symptom'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'symptom' => '故障现象',
        ];
    }

    /**
     * 获取故障现象列表
     * @param $select
     * @return array|string
     */
    public static function getSymptomIdNameArr($select = false)
    {
        $list = self::find()->select('id,symptom')->where(['is_del' => self::DEL_NO])->orderby('symptom')->asArray()->all();

        $result = ['' => '请选择'];
        $list   = $list ? Tools::map($list, 'id', 'symptom', null, 0) : '';

        if ($list) {
            foreach ($list as $key => $value) {
                $result[$key] = $value;
            }
        }
        return $select ? $result : $list;
    }

    /**
     * 根据故障现象id获取故障原因内容
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public static function getSymptomNameStr($id)
    {
        $id   = explode(',', $id);
        $list = self::find()->select('symptom')->where(['id' => $id])->asArray()->all();
        $str  = '';
        foreach ($list as $k => $v) {
            $str .= $v['symptom'] . '<br/>';
        }
        return $str;
    }

    /**
     *  通过ID查询相应的数据
     *  @param where
     **/
    public static function getEquipSymptomDetail($where)
    {
        return self::find()->where($where)->asArray()->one();
    }

}
