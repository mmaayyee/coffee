<?php

namespace backend\models;

use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "service_count".
 *
 * @property int $id
 * @property int $count 总人数
 * @property int $people 人工回复总数
 * @property int $create_time 创建时间
 */
class ServiceCount extends \yii\db\ActiveRecord
{
    public $count;
    public $people;
    public $create_time;
    public $begin_time;
    public $end_time;
    public $category;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_count';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['count', 'people'], 'integer'],
            [['create_time'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'count' => '总数',
            'people' => '客服人数(次数)',
            'create_time' => '日期',
            'category' => '类别',
        ];
    }

    /**
     *  获取全部统计数据
     * @return array
     */
    public static function getServiceCountList($params)
    {
        $page         = isset($params['page']) ? $params['page'] : 0;
        $list = ServiceQuestion::postBase("service-api/service-count-list",$params, '&page=' . $page);
        return !$list ? [] : Json::decode($list);
    }
}
