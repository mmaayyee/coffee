<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "app_version_management".
 *
 * @property integer $Id
 * @property string $big_screen_version
 * @property string $small_screen_version
 * @property integer $equip_type_id
 */
class AppVersionManagement extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_version_management';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equip_type_id'], 'integer'],
            [['big_screen_version', 'small_screen_version'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'big_screen_version' => '大屏版本号',
            'small_screen_version' => '小屏版本号',
            'equip_type_id' => '设备类型',
        ];
    }

    
    /**
     * 添加app发布版本信息
     * @author  zmy
     * @version 2017-08-28
     * @param   [string]     $equipTypeID [设备类型ID]
     * @return  [string]                  [true/false]
     */
    public static function saveAppVersionManagement($equipTypeID)
    {
        $appModel                = new AppVersionManagement();
        $appModel->equip_type_id = $equipTypeID;
        return $appModel->save();
    }
}
