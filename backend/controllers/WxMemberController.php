<?php

namespace backend\controllers;

use backend\models\ScmSupplier;
use backend\models\WxMemberSearch;
use common\helpers\WXApi\User;
use common\models\WxDepartment;
use common\models\WxMember;
use common\models\WxMemberChild;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * WxMemberController implements the CRUD actions for WxMember model.
 */
class WxMemberController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view', 'index', 'create', 'update', 'delete', 'sync-user', 'ajax-get-roles', 'ajax-get-department', 'parent-member'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all WxMember models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('成员列表')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new WxMemberSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WxMember model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('查看成员')) {
            return $this->redirect(['site/login']);
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new WxMember model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('添加成员')) {
            return $this->redirect(['site/login']);
        }
        $model           = new WxMember();
        $model->scenario = "create";
        $data            = Yii::$app->request->post('WxMember');
        if ($data && $model->load(['WxMember' => $data]) && $model->validate()) {
            $saveRes = WxMemberChild::saveMember($model, $data);
            if ($saveRes === true) {
                return $this->redirect(['view', 'id' => $model->userid]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing WxMember model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('编辑成员')) {
            return $this->redirect(['site/login']);
        }
        $model    = $this->findModel($id);
        $oldModel = (object) [
            'parent_id'   => $model->parent_id,
            'parent_path' => $model->parent_path,
            'org_id'      => $model->org_id,
            'position'    => $model->position,
            'userid'      => $model->userid,
        ];
        $model->scenario = "update";
        $data            = Yii::$app->request->post('WxMember');
        if ($data && $model->load(['WxMember' => $data]) && $model->validate()) {
            $saveRes = WxMemberChild::saveMember($model, $data, $oldModel);
            if ($saveRes === true) {
                return $this->redirect(['view', 'id' => $model->userid]);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing WxMember model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('删除成员')) {
            return $this->redirect(['site/login']);
        }
        WxMemberChild::deleteMember($id);
        return $this->redirect(['index']);
    }

    /**
     * Finds the WxMember model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return WxMember the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WxMember::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 同步用户信息
     * @return [type] [description]
     */
    public function actionSyncUser()
    {
        $result     = true;
        $wxUserObj  = new User();
        $data       = array('department_id' => 1, 'fetch_child' => 1, 'status' => 0);
        $wxUserList = $wxUserObj->partUserList($data);
        if (!is_array($wxUserList)) {
            $result = false;
        }

        foreach ($wxUserList as $v) {
            $v['avatar_mediaid'] = isset($v['avatar']) ? $v['avatar'] : '';
            $v['department_id']  = $v['department'] ? $v['department'][0] : '';
            $v['ctime']          = time();
            $_model              = WxMember::findOne($v['userid']);
            $_model              = empty($_model) ? new WxMember() : $_model;
            $positionArr         = array_flip(WxMember::$position);
            $v['position']       = empty($positionArr[$v['position']]) ? 0 : $positionArr[$v['position']];
            unset($v['extattr']);
            $_model->setAttributes($v);
            if (!$_model->save()) {
                $result = false;
            }
        }
        if ($result) {
            $searchModel  = new WxMemberSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            return $this->render('index', [
                'searchModel'  => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            die('同步失败');
        }
    }

    /**
     * 获取上级人员列表(该成员所在部门以及总裁办)
     * @return [type] [description]
     */
    public function actionParentMember()
    {
        // 获取当前成员所在部门id
        $departmentId    = Yii::$app->request->get('department_id', 0);
        $memberId        = Yii::$app->request->get('member_id');
        $supplierWaterId = Yii::$app->request->get('supplier_id');
        $position        = Yii::$app->request->get('position_id');
        $parentIdOption  = $supplierOption  = $positionOption  = ['' => '请选择'];
        if ($departmentId) {
            $deparIdArr[] = $departmentId;
            // 获取当前成员所在部门id的上级id以及分公司部门标识
            $wxDepartDetail = WxDepartment::getDepartDetail('org_id, parentid, headquarter', ['id' => $departmentId]);
            if ($wxDepartDetail) {
                $headquarter = $wxDepartDetail->headquarter;
                $orgId       = $wxDepartDetail->org_id;
                // 上级部门id
                $deparIdArr[] = $wxDepartDetail->parentid;

                // 获取总裁办部门id
                $deparIdArr[] = WxDepartment::getDepartId('id', ['headquarter' => WxDepartment::CHAIRMAN]);

                // 如果部门标识为供水商则返回该分公司下的供水商列表
                $supplierOption = $this->getWaterSupplierOption($headquarter, $orgId, $supplierWaterId);

                // 获取该部门对应的职位
                $positionOption = $this->getDepartPosition($headquarter, $orgId, $position);
                // 上级领导列表
                $parentIdOption = $this->getParentIdNameOption($deparIdArr, $memberId);
            }
        }
        return json_encode(['parentIdOption' => $parentIdOption, 'supplierOption' => $supplierOption, 'positionOption' => $positionOption]);
    }

    /**
     * 获取上级领导列表
     * @author  zgw
     * @version 2016-08-25
     * @param   array       $departIdArr 部门id列表
     * @param   string      $memberId    成员id
     * @return  string
     */
    private function getParentIdNameOption($departIdArr, $memberId)
    {
        $parentIdOption  = '';
        $departIdArr     = array_filter($departIdArr);
        $memberIdNameArr = WxMember::getMemberNameList(['department_id' => $departIdArr]);
        if ($memberIdNameArr) {
            foreach ($memberIdNameArr as $wxMemberId => $memberName) {
                $select = $wxMemberId == $memberId ? 'selected="selected"' : '';
                $parentIdOption .= "<option value='" . $wxMemberId . "' " . $select . ">" . $memberName . "</option>";
            }
        }
        return $parentIdOption;
    }

    /**
     * 获取供应商列表
     * @author  zgw
     * @version 2016-08-25
     * @param   int       $headquarter     部门标识
     * @param   int       $orgId           分公司id
     * @param   int       $supplierWaterId 供水商id
     * @return  string                     供水商列表
     */
    private function getWaterSupplierOption($headquarter, $orgId, $supplierWaterId)
    {
        $supplierOption = '';
        if ($headquarter == WxDepartment::WATERMINISTRY) {
            $where = ['type' => ScmSupplier::WATER];
            if ($orgId > 1) {
                $where = ['and', ['like', 'org_id', '-' . $orgId . '-'], ['type' => ScmSupplier::WATER]];
            }
            $getSupplierArray = ScmSupplier::getSupplierArray($where);
            if ($getSupplierArray) {
                foreach ($getSupplierArray as $supplierId => $supplierName) {
                    $select = $supplierId == $supplierWaterId ? 'selected="selected"' : '';
                    $supplierOption .= "<option value='" . $supplierId . "' " . $select . ">" . $supplierName . "</option>";
                }
            }
        }
        return $supplierOption;
    }

    /**
     * [getDepartPosition description]
     * @author  zgw
     * @version 2016-08-25
     * @param   int     $headquarter 部门标识
     * @param   int     $orgId       分公司id
     * @param   int     $position    职位id
     * @return  string               职位列表
     */
    private function getDepartPosition($headquarter, $orgId, $position)
    {
        $positionOption    = '<option value="">请选择</option>';
        $departPositionArr = WxDepartment::$partPostion;
        if (isset($departPositionArr[$headquarter])) {
            $positionArr        = $departPositionArr[$headquarter];
            $orgTotalPostionArr = [WxMember::EQUIP_MANAGER, WxMember::EQUIP_ASSISTANT, WxMember::DISTRIBUTION_MANAGER];
            foreach ($positionArr as $positionId => $positionName) {
                if ($orgId > 1 && in_array($positionId, $orgTotalPostionArr)) {
                    continue;
                }
                $select = $positionId == $position ? 'selected="selected"' : '';
                $positionOption .= "<option value='" . $positionId . "' " . $select . ">" . $positionName . "</option>";
            }
        }
        return $positionOption;
    }

}
