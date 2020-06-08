<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "distribution_notice_read".
 *
 * @property integer $Id
 * @property string $userId
 * @property integer $read_status
 * @property string $read_time
 * @property integer $read_feedback
 * @property string $notice_id
 */
class DistributionNoticeRead extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'distribution_notice_read';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['read_feedback'], 'string'],
            [['read_status', 'read_time', 'notice_id'], 'integer'],
            [['userId'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'userId' => '阅读人',
            'read_status' => '阅读状态',
            'read_time' => '阅读时间',
            'read_feedback' => '阅读反馈',
            'notice_id' => 'Notice ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDistributionNotice()
    {
        return $this->hasOne(DistributionNotice::className(), ['Id' => 'notice_id']);
    }

    public static function getDistributionNoticeList($field='*',$where=[])
    {   
        // 关联查询 joinWith('distributionNotice')
        return self::find()->select($field)->where($where)->orderBy('Id DESC')->all();
    }

}
