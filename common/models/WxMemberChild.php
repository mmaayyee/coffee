<?php
namespace common\models;

use backend\models\DistributionUser;
use backend\models\EquipRfidCard;
use backend\models\Manager;
use backend\models\ManagerLog;
use common\helpers\WXApi\User;
use Yii;

class WxMemberChild extends \yii\base\Model
{
    /**
     * 验证提交的职位是否为配送人员
     * @author zhenggangwei
     * @date   2019-08-23
     * @param  integer     $position 职位标识
     * @return boolean               true-配送人员 false-非配送人员
     */
    private static function isDistribution($position)
    {
        return in_array($position, [WxMember::DISTRIBUTION_MEMBER, WxMember::DISTRIBUTION_RESPONSIBLE]);
    }

    /**
     * 保存成员数据
     * @author zhenggangwei
     * @date   2019-08-24
     * @param  object     $model    成员对象
     * @param  array      $data     提交的数据
     * @param  object     $oldModel 提交数据之前成员对象
     * @return boolen|none
     */
    public static function saveMember($model, $data, $oldModel = null)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($oldModel) {
                $saveRes = self::updateMember($model, $oldModel, $data);
            } else {
                $saveRes = self::createMember($model, $data);
            }
            if ($saveRes === true) {
                $transaction->commit();
                return true;
            } else {
                Yii::$app->getSession()->setFlash('error', $saveRes);
                $transaction->rollBack();
                return $saveRes;
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e);
        }

    }

    /**
     * 添加成员
     * @author zhenggangwei
     * @date   2019-08-23
     * @param  array      $data  提交的数据
     * @param  object     $model 成员对象
     * @return string|boolen
     */
    private static function createMember($model, $data)
    {
        $model->create_time = time();
        $model->save();
        $model->parent_path = self::getParentPath($model);
        $model->save(false);
        // 添加配送员
        if (self::isDistribution($model->position) && !DistributionUser::addUser($model->userid, $model->org_id)) {
            return '添加配送员失败';
        }
        // 添加日志
        ManagerLog::saveLog(Yii::$app->user->id ?? 1, "成员管理", ManagerLog::UPDATE, $model->name);
        // 添加微信通讯录
        $res = self::addWxMember($data);
        if ($res != 'created') {
            return '成员添加接口失败' . $res;
        }
        return true;
    }

    /**
     * 更新成员信息
     * @author zhenggangwei
     * @date   2019-08-23
     * @param  object     $model    成员对象
     * @param  object     $oldModel 修改前成员对象
     * @param  array      $data     提交的数据
     * @return string|boolen
     */
    private static function updateMember($model, $oldModel, $data)
    {
        //更新管理员表中的分公司id
        if ($oldModel->org_id != $model->org_id && Manager::changeBranch($model->org_id, $model->userid) === false) {
            return '更新管理员表中的分公司id失败';
        }
        // 添加配送员
        if ($oldModel->position != $model->position) {
            if (self::isDistribution($oldModel->position) && (DistributionUser::delUser($oldModel->userid) === false && Building::delDistributionUser($oldModel->userid) == false)) {
                return '更新配送员失败';
            }
            if (self::isDistribution($model->position) && !DistributionUser::addUser($model->userid, $model->org_id)) {
                return '添加配送员失败';
            }
        }
        // 更新成员路径
        if ($oldModel->parent_id != $model->parent_id) {
            $model->parent_path = self::getParentPath($model);
            self::updateChildParentPath($model, $oldModel->parent_path);
        }
        $model->save();
        //添加操作日志 失败回滚
        ManagerLog::saveLog(Yii::$app->user->id ?? 1, "成员管理", ManagerLog::UPDATE, $model->name);
        // 添加微信通讯录
        $res = self::addWxMember($data, 'edit');
        if ($res != 'updated' && $res != 'created') {
            return '成员更新接口失败' . $res;
        }
        return true;
    }

    /**
     * 调用企业微信添加成员接口
     * @author zhenggangwei
     * @date   2019-08-23
     * @param  array      $data 提交的数据
     * @param  string     $type 类型 add-添加 edit-编辑
     */
    private static function addWxMember($data, $type = 'add')
    {
        $wxUserObj          = new User();
        $data['department'] = array($data['department_id']);
        $data['position']   = WxMember::$position[$data['position']];
        if ($type == 'add') {
            return $wxUserObj->userAdd($data);
        } else {
            return $wxUserObj->userEdit($data);
        }
    }

    /**
     * 获取成员路径
     * @author zhenggangwei
     * @date   2019-08-23
     * @param  object     $model 成员对象
     * @return string            成员路径
     */
    private static function getParentPath($model)
    {
        $parentPath = WxMember::getMemberDetail('parent_path', array('userid' => $model->parent_id))['parent_path'];
        return $parentPath ? $parentPath . $model->userid . '-' : '-' . $model->userid . '-';
    }

    /**
     * 更新成员子集路径
     * @author zhenggangwei
     * @date   2019-08-23
     * @param  object     $model         成员对象
     * @param  string     $oldParentPath 旧的成员路径
     * @return integer
     */
    private static function updateChildParentPath($model, $oldParentPath)
    {
        $sql = "update wx_member set parent_path=replace(parent_path,'" . $oldParentPath . "','" . $model->parent_path . "') where parent_path like '" . $oldParentPath . "%' and userid != '" . $model->userid . "';";
        return Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * 通过成员ID获取成员信息
     * @author zhenggangwei
     * @date   2019-08-23
     * @param  string     $userId 成员ID
     * @return object
     */
    public static function getMemberByUid($userId)
    {
        return WxMember::findOne($userId);
    }

    /**
     * 删除成员信息
     * @author zhenggangwei
     * @date   2019-08-24
     * @param  string     $userid 成员ID
     * @return boolen
     */
    public static function deleteMember($userid)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $delRes = self::disableMember($userid);
            if ($delRes === true) {
                $transaction->commit();
                return true;
            } else {
                Yii::$app->getSession()->setFlash('error', $delRes);
                $transaction->rollBack();
                return $delRes;
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e);
        }
    }

    /**
     * 禁用成员
     * @author zhenggangwei
     * @date   2019-08-24
     * @param  string     $userid 成员ID
     * @return string|boolen
     */
    private static function disableMember($userid)
    {
        $model = WxMember::findOne($userid);
        if (!$model) {
            return true;
        }
        // 删除成员
        $model->is_del = WxMember::DEL_YES;
        $model->save(false);
        // 查询门禁卡中是否有此人，如果有，禁用此卡。
        $retUpdateRfid = EquipRfidCard::IsExistWxMemberId($userid);
        if (!$retUpdateRfid) {
            return '此人所属门禁卡禁用失败';
        }
        //禁止用户登录系统
        Manager::forbidManager($userid);
        //添加操作日志
        ManagerLog::saveLog(Yii::$app->user->id ?? 1, "成员管理", ManagerLog::DELETE, $model->name);
        // 如果是配送员则清除配送员所属楼宇
        if (self::isDistribution($model->position)) {
            //删除该配送员负责的点位
            $buildSaveRes = Building::delDistributionUser($userid);
            // 删除配送员表中该配送员的数据
            $delDistributionUserRes = DistributionUser::delUser($userid);
            if ($buildSaveRes === false || $delDistributionUserRes === false) {
                return '负责楼宇清空失败';
            }
        }
        // 删除微信成员
        $wxUserObj = new User();
        $res       = $wxUserObj->userDel($userid);
        if ($res != 'deleted' && !strstr($res, 'userid not found')) {
            return '接口删除失败' . $res;
        }
        WxMemberTagAssoc::deleteUserTag($model->userid);
        return true;
    }
}
