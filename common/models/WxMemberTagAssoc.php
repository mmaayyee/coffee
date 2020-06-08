<?php

namespace common\models;

use common\helpers\WXApi\Tag;
use Yii;

/**
 * This is the model class for table "wx_member_tag_assoc".
 *
 * @property integer $id
 * @property string $userid
 * @property integer $tagid
 */
class WxMemberTagAssoc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wx_member_tag_assoc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wx_tagid', 'wx_memberid'], 'required'],
            [['wx_tagid'], 'integer'],
            [['wx_memberid'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'wx_memberid' => '成员',
            'wx_tagid'    => '标签',
        ];
    }
    /**
     * @param  $tagid 标签id
     * @return 标签成员列表
     */
    public static function getMemberVal($tagid)
    {
        $res = self::find()
            ->asArray()
            ->select(['wx_memberid'])
            ->where(['wx_tagid' => $tagid])
            ->all();
        $arr = [];
        foreach ($res as $v) {
            $arr[] = $v['wx_memberid'];
        }

        return $arr;
    }

    /**
     * 获取标签名称
     * @param  [type]
     * @return [type]
     */
    public static function getTagName($userid)
    {
        $res = self::find()
            ->asArray()
            ->select(['wx_tagid'])
            ->where(['wx_memberid' => $userid])
            ->all();
        $tagidArr = [];
        foreach ($res as $v) {
            $tagidArr[] = $v['wx_tagid'];
        }
        $tagNameArr = !empty($tagidArr) ? \common\models\WxTag::getTagName($tagidArr) : '';
        return $tagNameArr;
    }

    /**
     * 根据标签ID和成员ID检查成员是否在标签内
     * @author zhenggangwei
     * @date   2019-08-24
     * @param  integer     $userId 成员ID
     * @param  integer     $tagId  标签ID
     * @return object
     */
    private static function getUserTag($userId, $tagId)
    {
        return self::findOne(['wx_memberid' => $userId, 'wx_tagid' => $tagId]);
    }

    /**
     * 删除用户标签
     * @author zhenggangwei
     * @date   2019-08-24
     * @param  string      $userId 成员ID
     * @return integer
     */
    public static function deleteUserTag($userId)
    {
        return self::deleteAll(['wx_memberid' => $userId]);
    }

    /**
     * 添加用户到标签
     * @author zhenggangwei
     * @date   2019-08-23
     * @param  string      $userId 用户ID
     * @param  integer     $tagId  标签ID
     */
    public static function addUserToTag($userId, $tagId)
    {
        $isExists = self::getUserTag($userId, $tagId);
        if ($isExists) {
            return true;
        }
        $tagAssoc              = new self;
        $tagAssoc->wx_memberid = $userId;
        $tagAssoc->wx_tagid    = $tagId;
        $tagAssoc->save();
        $tagUserAdd                  = new Tag();
        $qywxTagUserData['tagid']    = $tagId;
        $qywxTagUserData['userlist'] = [$userId];
        $tagUserAdd->tagUserAdd($qywxTagUserData);
        return true;
    }
}
