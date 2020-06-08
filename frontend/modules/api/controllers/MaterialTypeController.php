<?php

namespace frontend\modules\api\controllers;

use backend\models\ScmMaterialType;

/**
 * 物料分类接口类
 */
class MaterialTypeController extends ApiBaseController
{
    public $enableCsrfValidation = false;

    /**
     * 获取物料分类列表
     * @author zhenggangwei
     * @date   2020-03-23
     * @return array
     */
    public function actionGetMaterialType()
    {
        $materialTypeList = ScmMaterialType::getOnlineMaterialType();
        return $this->success($materialTypeList);
    }

}
