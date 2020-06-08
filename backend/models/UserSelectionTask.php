<?php

namespace backend\models;

use Yii;
use yii\helpers\Json;
use backend\models\BuildingLevel;
use backend\models\RegistrationTime;
use backend\models\ConsumerGoods;
use backend\models\ConsumptionLevel;
use backend\models\PayTime;
use backend\models\ConsumptionCity;
use backend\models\cupAverage;
use backend\models\ConsumptionFrequency;
use common\models\Api;
use common\models\Equipments;
use backend\models\BuildType;
use common\models\TaskApi;

/**
 * This is the model class for table "user_selection_task".
 *
 * @property int $selection_task_id
 * @property string $selection_task_name 用户筛选任务名称
 * @property int $selection_task_status 状态，（0-未执行 1-正在执行，2-执行完成）
 * @property int $selection_task_result 执行结果（1-执行失败，2-执行成功）
 * @property string $mobile_num 号码数量（人数）
 * @property string $logic_relation 逻辑关系(条件之间)
 * @property string $single_query_where （单个）条件筛选
 * @property string $reference_task_id 参考任务ID
 * @property string $mobile_file_path 手机号文件路径
 * @property string $create_time 添加时间
 * @property string $validate_mobile 验证的手机号
 */
class UserSelectionTask extends \yii\db\ActiveRecord
{
    public $selection_task_id;      //  用户筛选任务ID
    public $selection_task_name;    //  用户筛选任务名称
    public $selection_task_status;  //  状态，（0-未执行 1-正在执行，2-执行完成）
    public $selection_task_result;  //  执行结果（1-执行失败，2-执行成功）
    public $mobile_num;             //  号码数量（人数）
    public $logic_relation;         //  逻辑关系(条件之间)
    public $single_query_where;     //  （单个）条件筛选
    public $reference_task_id;      //  参考任务ID
    public $mobile_file_path;       //  号码文件路径
    public $create_time;            //  添加时间
    public $validate_mobile;        //  验证的手机号
    public $start_time;             //  开始时间
    public $end_time;               //  结束时间
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['selection_task_id', 'selection_task_status', 'selection_task_result', 'mobile_num', 'reference_task_id', 'reference_task_id', 'create_time'], 'integer'],
            [['selection_task_name', 'mobile_file_path', 'validate_mobile'], 'string', 'max' => 255],
            [['logic_relation', 'single_query_where'], 'string', 'max' => 2000],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'selection_task_id'     => '用户筛选任务ID',
            'selection_task_name'   => '用户筛选任务名称',
            'selection_task_status' => '执行状态，（0-未执行 1-正在执行，2-执行完成）',
            'selection_task_result' => '执行结果（1-执行失败，2-执行成功）',
            'mobile_num'            => '号码数量（人数）',
            'logic_relation'        => '逻辑关系(条件之间)',
            'single_query_where'    => '（单个）条件筛选',
            'reference_task_id'     => '参考任务ID',
            'mobile_file_path'      => '手机号文件路径',
            'create_time'           => '添加时间',
            'validate_mobile'       => '验证的手机号',
        ];
    }

    /**
     * 获取逻辑条件编号数组
     * @author  zmy
     * @version 2018-01-05
     * @return  [type]     [description]
     */
    public static function logicConditionList()
    {
        return [
            '1' => '楼宇点位',
            '2' => '注册时间',
            '3' => '最近消费商品',
            '4' => '消费点位',
            '5' => '消费时间',
            '6' => '消费城市',
            '7' => '均杯价',
            '8' => '消费频次',
            '9' => '企业导入',
            '10'=> '导入用户',
            '11'=> '已有任务',
        ];
    }
    

    /**
     * 用户筛选任务状态数组
     * @author  zmy
     * @version 2018-01-11
     * @return  [type]     [description]
     */
    public static function getTaskStatusList()
    {
        return [
            // （0-未执行 1-正在执行，2-执行完成）
            '' => '请选择',
            '0'=> '未执行',
            '1'=> '正在执行',
            '2'=> '执行完成',
        ];
    }

    /**
     * 获取用户筛选任务结果数组
     * @author  zmy
     * @version 2018-02-07
     * @return  [type]     [description]
     */
    public static function getTaskResultList()
    {
        return [
            '' => '请选择',
            '1'=> '执行失败',
            '2'=> '执行成功',
        ];
    }

    // 获取 楼宇类型接口，（已有请选择）： $arr = Api::getBuildTypeList();
    // 获取 分公司接口，$arr  = Api::getOrgNameList();
    // 获取 设备类型， $arr = Api::getEquipTypeList();
    
    /**
     * 获取需要的产品组信息
     * @author  zmy
     * @version 2018-01-12
     * @return  [type]     [description]
     */
    public static function getProducts()
    {
        $productList = Json::decode(Api::getProductList()); // 获取单品
        $list = [];
        foreach ($productList as $key => $value) {
            if($value['cf_product_status'] ==0 && $value['cf_source_id'] == 0){
                $list[$value['cf_product_id']] = $value['cf_product_name'];
            }
        }
        return $list;
    }
    /**
     * 获取所有单品信息
     * @author  wbq
     * @version 2018-6-7
     * @return  [type]     [description]
     */
    public static function getAllProducts()
    {
        $productList = Json::decode(Api::getProductList()); // 获取单品
        $list = [];
        foreach ($productList as $key => $value) {
            if($value['cf_source_id'] == 0) {
                $list[$value['cf_product_id']] = $value['cf_product_name'];
            }
        }
        return $list;
    }
    /**
     * 组合所有条件的接口数组
     * @author  zmy
     * @version 2018-01-12
     * userSelectionTaskId [0-添加时的数据, 其余,接口获取修改时的模板],
     * @return  [type]     [description]
     */
    public static function getConditionsList($userSelectionTaskId = 0)
    {
        $branchList    = Api::getOrgNameList(['organization_type'=>0]); // 分公司
        
        $equipTypeList = Api::getEquipTypeList(); // 设备类型;
        unset($equipTypeList['']);

        //$productList = UserSelectionTask::getProducts(); // 获取状态正常的单品
        /*
         *  更改为获取所有状态单品
         *  @auth wbq  2018-6-7
         * */
        $productList = UserSelectionTask::getAllProducts(); // 获取所有状态的单品
        $buildings   = Api::getBuildIdNameList(); //获取建筑名称数组
        $buildingList= [];
        
        foreach($buildings as $id => $name){
            $buildingList[] = ['id' => $id, 'value' => $name];
        }
        
        $companys= Api::getIdToCompanysNameList(); // 获取公司数组
        unset($companys['']);
        foreach($companys as $id => $name){
            $companyList[] = ['id' => $id, 'value' => $name];
        }
        
        $referenceTask    = Api::getUserSelectionTaskIdToNameList(); // 参考任务数组
        // echo "<pre/>";
        $completeTaskList = Api::getUserSelectionTaskIdToNameList(['selection_task_result'=>2]); // 已有任务名称数组（完成的任务）
        // var_dump($completeTaskList);die();
        
        $buildTypeList    = BuildType::getBuildType(); // 楼宇类型
        unset($buildTypeList['']);
        // 城市数组
        $cityList = Api::getOrgCityList();

        // 逻辑条件类型数组
        $conditionType = [
            '1' => '楼宇点位',
            '2' => '注册时间',
            '3' => '最近消费商品',
            '4' => '消费点位',
            '5' => '消费时间',
            '6' => '消费城市',
            '7' => '杯均价',
            '8' => '消费频次',
            '9' => '企业导入',
            '10'=> '已有任务',
            '11'=> '导入用户',
            '12'=> '订单时间',
        ];

        $referenceTaskList = [];
        $i = 0;
        foreach ($referenceTask as $key => $value) {
            $referenceTaskList[$i]['id']   = $key;
            $referenceTaskList[$i]['name'] = $value;
            $i++;
        }
        // 添加时的模板数据参数
        $updateConditionTemp = [];
        // 筛选任务名称
        $selectionTaskName   = '';
        // 参考任务id
        $reference_task      = '';
        // 验证的手机号码
        $validateMobile      = [];
        if($userSelectionTaskId){
            // 接口获取修改时的模板， 接口返回2个参数，1-updateConditionTemp, 2-
            $selectionTaskInfo   = TaskApi::getWhereByTaskId($userSelectionTaskId);
            //区分参考任务列表
            $reference_task      = $selectionTaskInfo['reference_task_id'];
            $updateConditionTemp = $selectionTaskInfo['updateConditionTemp'];
            $selectionTaskName   = $selectionTaskInfo['selection_task_name'];
            $validateMobile      = $selectionTaskInfo['validate_mobile'];
        }

        // 跳转添加页面时，进行传输的数据
        $taskOptionsList = [
            'buildingList'        => $buildingList,
            'companyList'         => $companyList,
            'selection_task_id'   => $userSelectionTaskId,
            'selection_task_name' => $selectionTaskName,
            'validate_mobile'     => $validateMobile,
            'reference_task_id'   => $referenceTaskList,
            'reference_task'      => !empty($reference_task) ? intval($reference_task) : '',
            'conditionTypeList'   => $conditionType,
            'addConditionTypeTemp'=> [
                    // 楼宇点位
                    '1' => [
                        'cityList' => $cityList,
                        'buildTypeList'=> $buildTypeList,
                        'equipTypeList'=> $equipTypeList,
                    ],
                    // 注册时间
                    '2' => [],
                    '3' => [
                        'consumeGoodList'=> $productList,
                        'equipTypeList'=> $equipTypeList,
                    ],
                    '4' => [
                        'cityList' => $cityList,
                        'buildTypeList'=> $buildTypeList,
                        'equipTypeList'=> $equipTypeList,
                        
                    ],
                    '5' => [],
                    '6' => [
                        'cityList' => $cityList,
                    ],
                    '7' => [],
                    '8' => [],
                    '9' => [],
                    '10'=> [
                        'taskList' => $completeTaskList,
                    ],
                    '11'=> [],
                    '12'=> [
                        'orderNumLogic' => [
                            'eq'   =>  '等于',
                            'geq'  =>  '大于等于',
                            'leq'  =>  '小于等于',
                        ],
                        'isPay' => [
                            2 => '全部',
                            0 => '免费',
                            1 => '付费',
                        ]
                    ],
                ],
            'updateConditionTemp' => $updateConditionTemp,
        ];

        return $taskOptionsList;
    }


}
