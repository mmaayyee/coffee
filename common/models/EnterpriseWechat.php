<?php
namespace common\models;

use Yii;
use yii\helpers\Json;

/**
 * 企业微信接口类
 */
class EnterpriseWechat
{
    /**
     * 保存成员
     * @author zhenggangwei
     * @date   2019-08-24
     * @return boolen|string true-保存成功 string-保存失败并给出提示信息
     */
    public static function saveMember()
    {
        $data = file_get_contents("php://input");
        $data = Json::decode($data);
        if (!$data) {
            return '参数不能为空';
        }
        $oldMember        = WxMemberChild::getMemberByUid($data['userid']);
        $member           = $oldMember ? $oldMember : new WxMember();
        $data['is_del']   = WxMember::DEL_NO;
        $data['position'] = WxMember::DISTRIBUTION_RESPONSIBLE;
        $departmentList   = WxDepartment::getDepartIds(WxDepartment::DISTRIBUTION_DEPARTMENT, $data['org_id']);
        if ($departmentList) {
            $data['department_id'] = $departmentList[0];
        } else {
            return '请先添加部门';
        }
        if ($oldMember) {
            $oldMember = (object) [
                'parent_id'   => $member->parent_id,
                'parent_path' => $member->parent_path,
                'org_id'      => $member->org_id,
                'position'    => $member->position,
                'userid'      => $member->userid,
            ];
        }
        // 验证数据
        if (!$member->load(['WxMember' => $data]) || !$member->validate()) {
            return Json::encode($member->getErrors());
        }
        // 保存数据
        $saveRes = WxMemberChild::saveMember($member, $data, $oldMember);
        if ($saveRes !== true) {
            return $saveRes;
        }
        // 将用户添加到标签
        return WxMemberTagAssoc::addUserToTag($member->userid, Yii::$app->params['tagId']);
    }

    /**
     * 删除成员
     * @author zhenggangwei
     * @date   2019-08-24
     * @return boolen             true-删除成功 false-删除失败
     */
    public static function deleteMember()
    {
        $userid = Yii::$app->request->get('userid');
        $delRes = WxMemberChild::deleteMember($userid);
        if ($delRes === true) {
            return true;
        }
        return $delRes;
    }
}
