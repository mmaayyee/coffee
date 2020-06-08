<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wx_tag".
 *
 * @property integer $id
 * @property string $name
 * @property integer $ctime
 * @property integer $utime
 */
class WxTag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wx_tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tagname'], 'string', 'max' => 50],
            [['tagname'], 'required'],
            [['tagname'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tagid'   => '标签编号',
            'tagname' => '标签名称',
        ];
    }

    /**
     * 根据标签id获取标签名称
     * @param  [type]
     * @return [type]
     */
    public static function getTagName($tagid)
    {
        $res = self::find()
            ->asArray()
            ->select(['tagname'])
            ->where(['tagid' => $tagid])
            ->all();
        $tagNameStr = '';
        foreach ($res as $v) {
            $tagNameStr .= $v['tagname'] . '，';
        }
        $tagNameStr = trim($tagNameStr, '，');
        return $tagNameStr;
    }
    /**
     * 获取标签列表
     * @return [type]
     */
    public static function getTagList()
    {
        $res = self::find()
            ->asArray()
            ->all();
        $tagList = '';
        foreach ($res as $v) {
            $tagList[$v['tagid']] = $v['tagname'];
        }
        return $tagList;
    }
}
