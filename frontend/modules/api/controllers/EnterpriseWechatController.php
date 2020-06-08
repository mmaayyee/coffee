<?php

namespace frontend\modules\api\controllers;

use common\models\EnterpriseWechat;

/**
 * 企业微信对外接口类
 */
class EnterpriseWechatController extends ApiBaseController
{
    public $enableCsrfValidation = false;

    /**
     * 保存成员数据
     * @author zhenggangwei
     * @date   2019-07-04
     * @return string
     */
    public function actionSaveMember()
    {
        $saveRes = EnterpriseWechat::saveMember();
        if ($saveRes === true) {
            return $this->success();
        } else {
            return $this->error(5, $saveRes);
        }
    }

    /**
     * 禁用成员
     * @author zhenggangwei
     * @date   2019-07-04
     * @return string
     */
    public function actionDisableMember()
    {
        $saveRes = EnterpriseWechat::deleteMember();
        if ($saveRes === true) {
            return $this->success();
        } else {
            return $this->error(5, $saveRes);
        }
    }
}
