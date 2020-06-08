<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "cache_simple".
 *
 * @property string $cache_id 缓存ID
 * @property string $cache_key 缓存key值
 * @property string $cache_content 缓存内容
 * @property string $cache_description 缓存名称
 * @property string $update_time 更新时间
 * @property string $effective_time 有效时间
 */
class CacheSimple extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cache_simple';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['update_time', 'effective_time'], 'integer'],
            [['effective_time'], 'required'],
            [['cache_key'], 'string', 'max' => 100],
            [['cache_content', 'cache_description'], 'string', 'max' => 300],
            [['cache_key'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cache_id' => 'Cache ID',
            'cache_key' => 'Cache Key',
            'cache_content' => 'Cache Content',
            'cache_description' => 'Cache Description',
            'update_time' => 'Update Time',
            'effective_time' => 'Effective Time',
        ];
    }
}
