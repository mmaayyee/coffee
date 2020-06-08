<?php

namespace backend\models;

use common\helpers\Tools;
use common\models\Api;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "build_type".
 *
 * @property integer $id
 * @property string $type_name
 * @property string $create_date
 */
class BuildType extends \yii\db\ActiveRecord
{
    public $type_name;
    public $type_code;
    public $id;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_name'], 'required'],
            [['type_name'], 'string', 'max' => 100],
            [[ 'type_code'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type_name' => '类型名称',
            'type_code' => '楼宇类型编码',
        ];
    }

    /**
     * 获取楼宇类别
     * @author wangxl
     * @param int $typeId
     * @param int $typeId
     * @return array
     */
    public static function getBuildType($typeId = '')
    {
        $buildList = Json::decode(Api::getBuildType(), 1);
        $buildArr  = Tools::map($buildList, 'id', 'type_name');
        if ($typeId !== '') {
            return isset($buildArr[$typeId]) ? $buildArr[$typeId] : '';
        } else {
            return $buildArr;
        }
        return $buildTypeArr;
    }

    /**
     * 根据场景id获取场景名称
     * @author  zgw
     * @version 2017-09-28
     * @param   integer     $ID 场景id
     * @return  string          场景名称
     */
    public static function getBuildTypeName($ID)
    {
        $buildTypeInfo = Api::getBuildTypeInfo($ID);
        if (!$buildTypeInfo) {
            return '';
        }
        $buildTypeInfo = Json::decode($buildTypeInfo);
        return $buildTypeInfo['type_name'];
    }
    /**
     * 根据场景id获取场景名称
     * @author  zgw
     * @version 2017-09-28
     * @param   integer     $ID 场景id
     * @return  string          场景名称
     */
    public static function getBuildTypeCode($ID)
    {
        $buildTypeInfo = Api::getBuildTypeInfo($ID);
        if (!$buildTypeInfo) {
            return '';
        }
        $buildTypeInfo = Json::decode($buildTypeInfo);
        return $buildTypeInfo['type_code'];
    }
}
