<?php

namespace backend\models;

use backend\models\BuildingRecord;
use backend\models\Manager;
use common\models\PointEvaluationApi;
use Yii;
use yii\helpers\Json;

/**
 * This is the model class for table "point_evaluation".
 *
 * @property int $id
 * @property string $point_name 点位名称（楼宇名称+摆放位置）
 * @property int $org_id 点位所在分公司
 * @property string $point_applicant 点位申请人（创建人）
 * @property string $point_position 点位中设备的摆放位置
 * @property int $point_level 点位评级
 * @property int $cooperate 合作方式
 * @property int $point_status 审批状态
 * @property int $build_type_id 渠道类型
 * @property int $build_record_id 楼宇管理ID
 * @property string $point_basic_info 点位基础信息
 * @property string $point_score_info 点位评分条件信息
 * @property string $point_other_info 点位其他信息
 * @property string $point_licence_pic 点位中清晰的水牌照片
 * @property string $point_position_pic 设备具投放的照片位置
 * @property string $point_company_pic 本楼宇中公司的照片
 * @property string $point_plan 平面图（非必填）
 * @property int $created_at 点位创建时间
 */
class PointEvaluation extends \yii\db\ActiveRecord
{

    const BD                  = 'BD'; // BD 人员提交审核
    const BDM                 = 'BDM'; // BDM
    const REGIONAL_RETAIL     = '区域零售'; // 区域零售 人员提交审核
    const HEADQUARTERS_RETAIL = '总部零售'; // 总部零售 人员提交审核
    const DIRECTOR_RETAIL     = '零售总监'; // 零售总监 人员提交审核
    const WAIT_HANDLE         = 2; // 待处理
    const HANDLE              = 1; // 已处理

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'point_evaluation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['org_id', 'point_level', 'cooperate', 'point_status', 'build_type_id', 'build_record_id', 'created_at'], 'integer'],
            [['point_applicant', 'point_position', 'cooperate', 'created_at'], 'required'],
            [['point_name', 'point_applicant', 'point_position'], 'string', 'max' => 50],
            [['point_basic_info', 'point_other_info', 'point_licence_pic', 'point_position_pic', 'point_company_pic'], 'string', 'max' => 500],
            [['point_score_info'], 'string', 'max' => 1000],
            [['point_plan'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                 => 'ID',
            'point_name'         => 'Point Name',
            'org_id'             => 'Org ID',
            'point_applicant'    => 'Point Applicant',
            'point_position'     => 'Point Position',
            'point_level'        => 'Point Level',
            'cooperate'          => 'Cooperate',
            'point_status'       => 'Point Status',
            'build_type_id'      => 'Build Type ID',
            'build_record_id'    => 'Build Record ID',
            'point_basic_info'   => 'Point Basic Info',
            'point_score_info'   => 'Point Score Info',
            'point_other_info'   => 'Point Other Info',
            'point_licence_pic'  => 'Point Licence Pic',
            'point_position_pic' => 'Point Position Pic',
            'point_company_pic'  => 'Point Company Pic',
            'point_plan'         => 'Point Plan',
            'created_at'         => 'Created At',
        ];
    }
    /**
     * web 端创建点位评分需要的渠道类型列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-17
     * @return    [json]     [渠道类型列表]
     */
    public static function getBuildTypeList()
    {
        return PointEvaluationApi::getBuildTypeObj();
    }
    /**
     * web 端创建点位评分 根据渠道类型和分公司ID 展示的楼宇已经创建的楼宇名称列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-17
     * @param     [int]     $buildTypeID [渠道类型ID]
     * @param     [int]     $orgID       [分公司ID]
     * @return    [json]                  [楼宇ID =》楼宇名称+状态]
     */
    public static function getBuildNameList($buildTypeID, $orgID)
    {
        return PointEvaluationApi::getBuildingNameList($buildTypeID, $orgID);
    }
    /**
     * web 端创建点位评分 根据选择的楼宇返回楼宇相关的详细信息
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-18
     * @param     [int]     $buildRecordID [楼宇ID]
     * @return    [json]                    [楼宇相关的详细信息]
     */
    public static function getCreateBuildRecordInfo($buildRecordID)
    {
        return PointEvaluationApi::getCreateBuildRecordInfo($buildRecordID);
    }

    /**
     * web 端创建点位评分 接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-18
     * @param:    [param]
     * @param     [array]     $paramsInfo [创建信息]
     * @return    [json]                 [创建结果]
     */
    public static function insertPointEvaluation($paramsInfo)
    {
        // 清晰的水牌照片
        $paramsInfo['point_licence_pic'] = BuildingRecord::uploadRecordImg($paramsInfo['point_licence_pic']);
        // 设备具投放的照片位置
        $paramsInfo['point_position_pic'] = BuildingRecord::uploadRecordImg($paramsInfo['point_position_pic']);
        // 点位楼宇中公司的照片
        $paramsInfo['point_plan'] = BuildingRecord::uploadRecordImg($paramsInfo['point_plan']);
        // 平面图非必填
        $paramsInfo['point_company_pic'] = BuildingRecord::uploadRecordImg($paramsInfo['point_company_pic']);

        return PointEvaluationApi::savePointEvaluation($paramsInfo);
    }
    /**
     * 获取点位评分列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-13
     * @param     [int]     $orgID [登陆分公司ID]
     * @return    [array]            [评分列表]
     */
    public static function getIndex($orgID)
    {

        $roleList = self::findRoleList();
        $roleName = Yii::$app->user->identity->role;
        // 判断登陆的角色
        if ($roleName == 'BD') {
            $pointList                          = PointEvaluationApi::getPointIndex($orgID, Yii::$app->user->identity->userid);
            $pointList['data']['handle_status'] = [];
            $pointList['data']['roleList']      = $roleList;
            return Json::encode($pointList);
        }
        $pointList = PointEvaluationApi::getPointIndex($orgID);

        $pointList['data']['handle_status'] = self::getHandleStatusList();
        // 转交人员名称
        $pointList['data']['newCreator'] = self::findCreatorNameList($orgID);
        // 提交审核人列表
        $pointList['data']['approver'] = self::getManagerApproverListByOrg($orgID);
        // 权限列表
        $pointList['data']['roleList'] = $roleList;
        return self::checkVerify($pointList);
    }
    /**
     * 获取转交人列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-25
     * @param     [type]     $orgID [description]
     * @return    [type]            [description]
     */
    private static function findCreatorNameList($orgID)
    {
        return Manager::getManagerBdListByOrg($orgID);
    }
    /**
     * 获取审核人名称列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-25
     * @param     [type]     $orgID [description]
     * @return    [type]            [description]
     */
    private static function getManagerApproverListByOrg($orgID)
    {
        return Manager::getManagerApproverListByOrg($orgID);
    }
    /**
     * 更新修改获取点位信息
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-21
     * @param     [type]     $pointID [description]
     * @return    [type]              [description]
     */
    public static function getUpdatePoint($pointID)
    {
        return PointEvaluationApi::getUpdatePointByID($pointID);
    }

    /**
     * 初始化列表权限列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-19
     * @return    [type]     [description]
     */
    private static function findRoleList()
    {
        $roleList = [
            'export'   => false,
            'update'   => false,
            'transmit' => false,
            'create'   => false,
            'view'     => false,
        ];
        if (Yii::$app->user->can('点位评估导出')) {
            $roleList['export'] = true;
        }
        if (Yii::$app->user->can('点位评估修改')) {
            $roleList['update'] = true;
        }

        if (Yii::$app->user->can('点位评估转交')) {
            $roleList['transmit'] = true;
        }
        if (Yii::$app->user->can('点位评估创建')) {
            $roleList['create'] = true;
        }
        if (Yii::$app->user->can('点位评估详情查看')) {
            $roleList['view'] = true;
        }
        return $roleList;
    }
    /**
     * 处理搜索条件增加
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-19
     * @return    [type]     [description]
     */
    private static function getHandleStatusList()
    {
        return [
            [
                'handleName'  => self::HANDLE,
                'handleValue' => '已处理',
            ],
            [
                'handleName'  => self::WAIT_HANDLE,
                'handleValue' => '待处理',
            ],
        ];
    }
    /**
     * 搜索点位评分列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-18
     * @return    [type]     [description]
     */
    public static function webSearchPointList($searchParams)
    {
        $searchParams = self::searchCriteria($searchParams);
        $points       = PointEvaluationApi::webSearchPointList($searchParams);
        $pointList    = Json::decode($points);
        return self::checkVerify($pointList);
    }

    /**
     * 验证是否有审核权限
     * @author zhenggangwei
     * @date   2019-05-28
     * @param  array     $pointList 点位评分列表
     * @return string
     */
    private static function checkVerify($pointList)
    {
        $roleName = Yii::$app->user->identity->role;
        if ($roleName != 'BD' && !empty($pointList['data']['pointList']['pointArray'])) {
            $checkRole = '';
            switch ($roleName) {
                case 'BDM':
                    $checkRole = 'BD';
                    break;
                case '区域运维主管':
                    $checkRole = 'BDM';
                    break;
                case '区域零售':
                    $checkRole = '区域运维主管';
                    break;
                case '总部零售':
                    $checkRole = '区域零售';
                    break;
                case '总部零售总监':
                    $checkRole = '总部零售';
                    break;
                default:
                    $checkRole = '其它';
                    break;
            }
            foreach ($pointList['data']['pointList']['pointArray'] as &$pointEvaluation) {
                if (!in_array($pointEvaluation['point_status'], ['提交审批', '审批中'])) {
                    continue;
                }
                if ($pointEvaluation['approval_role'] == $checkRole && Yii::$app->user->can('点位评估审核')) {
                    $pointEvaluation['evaluate'] = true;
                } else {
                    $pointEvaluation['evaluate'] = false;
                }
                unset($pointEvaluation);
            }
        }
        return Json::encode($pointList);
    }
    /**
     * 导出搜索的接口
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-20
     * @param     [array]     $searchParams [搜索传入的条件]
     * @return    [type]                   [description]
     */
    public static function webExportPoint($searchParams)
    {
        echo '<pre/>';
        $searchParams = self::searchCriteria($searchParams);
        $pointList    = PointEvaluationApi::webExportPoint($searchParams);
        if (!empty($pointList)) {
            $objPHPExcel = new \PHPExcel();
            $objPHPExcel->getProperties()->setCreator("咖啡零点吧-点位评分列表信息");
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '点位名称')
                ->setCellValue('B1', '分公司')
                ->setCellValue('C1', '渠道类型')
                ->setCellValue('D1', '点位级别')
                ->setCellValue('E1', '点位状态')
                ->setCellValue('F1', '提交人')
                ->setCellValue('G1', '创建时间')
            // ->setCellValue('H1', '联系人姓名')
            // ->setCellValue('I1', '联系人号码')
            // ->setCellValue('G1', '省份')
            // ->setCellValue('K1', '城市')
            // ->setCellValue('L1', '区域')
            // ->setCellValue('M1', '详细地址')
            // ->setCellValue('N1', '首杯免费策略')
            // ->setCellValue('O1', '首杯策略变更日期')
            // ->setCellValue('P1', '首杯备份策略')
            // ->setCellValue('Q1', '是否支持外送')
            // ->setCellValue('U1', '是否有任务支付')
            ;
            $i = 2;
            foreach ($pointList as $key => $point) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $point['point_name'])
                    ->setCellValue('B' . $i, $point['org_name'])
                    ->setCellValue('C' . $i, $point['type_name'])
                    ->setCellValue('D' . $i, $point['point_level'])
                    ->setCellValue('E' . $i, $point['point_status'])
                    ->setCellValue('F' . $i, $point['point_applicant'])
                    ->setCellValue('G' . $i, $point['created_at'])
                // ->setCellValue('H' . $i, $point['contact_name'])
                // ->setCellValue('I' . $i, $point['contact_tel'])
                // ->setCellValue('J' . $i, $point['province'])
                // ->setCellValue('K' . $i, $point['city'])
                // ->setCellValue('L' . $i, $point['area'])
                // ->setCellValue('M' . $i, $point['address'])
                // ->setCellValue('N' . $i, $point['first_free_strategy'])
                // ->setCellValue('O' . $i, $point['strategy_change_date'])
                // ->setCellValue('P' . $i, $point['first_backup_strategy'])
                // ->setCellValue('Q' . $i, $point['is_delivery'])
                // ->setCellValue('U' . $i, $point['is_has_task_pay'])
                ;
                $i++;
            }
            $objWriter      = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $callStartTime  = microtime(true);
            $outputFileName = "点位评分信息列表-" . date("Y-m-d") . ".xlsx";
            ob_end_clean();
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header('Content-Disposition:inline;filename="' . $outputFileName . '"');
            header("Content-Transfer-Encoding: binary");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Pragma: no-cache");
            $objWriter->save('php://output');
            exit;
        }
    }
    /**
     * 搜索点位评分 组装条件
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-20
     * @param     [array]     $searchParams [搜索传入的条件]
     * @return    [array]                   [搜索组装好的条件]
     */
    private static function searchCriteria($searchParams)
    {
        // 判断登陆的角色
        if (Yii::$app->user->identity->role == 'BD') {
            $searchParams['point_applicant'] = Yii::$app->user->identity->userid;
        }
        $searchParams['approval_role'] = '';
        // 判断搜索审核的条件
        if (!empty($searchParams['handle_status'])) {
            if ($searchParams['handle_status'] == self::WAIT_HANDLE) {
                $searchParams['approval_role'] = self::searchApprovalRole(Yii::$app->user->identity->role);
            }
            if ($searchParams['handle_status'] == self::HANDLE) {
                $searchParams['approval_role'] = Yii::$app->user->identity->role;
            }
        }
        return $searchParams;
    }
    /**
     * 点位评分转交记录方法
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-13
     * @param     [array]     $transferInfo [转交信息]
     * @return    [array]                   [转交结果]
     */
    public static function transferPointList($transferInfo)
    {
        $roleName  = Yii::$app->user->identity->role;
        $roleID    = Yii::$app->user->identity->userid;
        $newUserId = $transferInfo['transferInfo']['new_creator_name'];
        $transfer  = [
            'transferPoint' => [
                'transferInfo' => [
                    'new_creator_name' => $newUserId,
                    'role_ame'         => $roleName,
                    'transfer_id'      => $roleID,
                    'org_id'           => Manager::getOrgIDByUser($newUserId),
                ],
                'pointIDList'  => $transferInfo['transferPointList'],
            ],
        ];
        return PointEvaluationApi::transferPoint($transfer);
    }
    /**
     * 点位评分详情
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-11
     * @param     [int]     $pointID [点位评分ID]
     * @return    [json]              [点位评分详情]
     */
    public static function getView($pointID)
    {
        // 详情展示的所有数据
        $pointInfo = PointEvaluationApi::getPointInfoByID($pointID);
        if (empty($pointInfo)) {
            $list = [
                'error_code' => 1,
                'msg'        => '点位记录为空',
            ];
            return Json::encode($list);
        }
        // 判断权限 评审的流程
        if ($pointInfo['approvalRole']) {
            if (!Yii::$app->user->can('点位评估审核')) {
                $pointInfo['approvalRole'] = 0;
            } else {
                $ApprovalRole = self::getApprovalRole(end($pointInfo['approvalInfo'])['role_name']);
                // 登陆人的角色
                if ($ApprovalRole == Yii::$app->user->identity->role) {
                    $pointInfo['approvalRole'] = 1;
                } else {
                    $pointInfo['approvalRole'] = 0;
                }
            }
        } else {
            $pointInfo['approvalRole'] = 0;
        }
        $list = [
            'error_code' => 0,
            'msg'        => 'succeess',
            'data'       => $pointInfo,
        ];
        return Json::encode($list);
    }
    /**
     * 审核方法
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-19
     * @param     [array]     $pointApprovalInfo [审核内容]
     * @return    [json]                        [审核结果]
     */
    public static function pointApproval($pointApprovalInfo)
    {
        $pointApproval['approvalInfo'] = [
            'role_name'       => Yii::$app->user->identity->role,
            'approver'        => Yii::$app->user->identity->userid,
            'approver_status' => $pointApprovalInfo['approver_status'],
            'approver_msg'    => $pointApprovalInfo['approver_msg'],
            'approval_level'  => $pointApprovalInfo['approval_level'],
            'point_id'        => $pointApprovalInfo['point_id'],
        ];
        return PointEvaluationApi::pointApproval($pointApproval);
    }
    /**
     * 企业微信进入自己创建的点位评分列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-22
     * @param     [type]     $userInfo       [角色名称和分公司名称]
     * @param     [type]     $pointApplicant [创建人名称（登陆人）]
     * @return    [type]                     [description]
     */
    public static function weChatPointList($userInfo, $pointApplicant)
    {
        if ($userInfo['role'] != 'BD') {
            $pointApplicant = '';
        }
        return PointEvaluationApi::weChatPointList($userInfo['branch'], $pointApplicant);
    }
    /**
     * 企业微信搜索自己创建的点位评分列表
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-22
     * @param     [type]     $userInfo       [角色名称和分公司名称]
     * @param     [type]     $pointApplicant [创建人名称（登陆人）]
     * @return    [type]                     [description]
     */
    public static function searchPoint($searchPoint)
    {
        return PointEvaluationApi::weChatSearchPointList($searchPoint);
    }
    /**
     * 本点位评审的流程
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-11
     * @param     [string]     $approvalRole [角色名称]
     * @return    [string]                   [轮到评审的角色名称]
     */
    private static function getApprovalRole($approvalRole)
    {
        switch ($approvalRole) {
            case self::BD: // BD 人员提交审核
                return self::BDM;
            case self::BDM: // BDM 审批流程
                return self::REGIONAL_RETAIL;
            case self::REGIONAL_RETAIL: //   区域零售 审批流程
                return self::HEADQUARTERS_RETAIL;
            case self::HEADQUARTERS_RETAIL: // 总部零售 审批流程
                return self::DIRECTOR_RETAIL;
            case self::DIRECTOR_RETAIL: // 零售总监 审批流程
                return '';
            default:
                return '';
        }
    }
    /**
     *  本点位搜索条件角色的流程
     * @Author:   GaoYongLi
     * @DateTime: 2018-12-11
     * @param     [string]     $approvalRole [角色名称]
     * @return    [string]                   [轮到评审的角色名称]
     */
    private static function searchApprovalRole($approvalRole)
    {
        switch ($approvalRole) {
            case self::BDM: // BDM进入只显示 审批走到BD的
                return self::BD;
            case self::REGIONAL_RETAIL: // REGIONAL_RETAIL 搜索
                return self::BDM;
            case self::HEADQUARTERS_RETAIL:
                return self::REGIONAL_RETAIL;
            case self::DIRECTOR_RETAIL:
                return self::HEADQUARTERS_RETAIL;
            case self::DIRECTOR_RETAIL:
                return '';
            default:
                return '';
        }
    }
}
