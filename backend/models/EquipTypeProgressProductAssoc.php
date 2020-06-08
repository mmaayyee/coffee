<?php

namespace backend\models;
use backend\models\EquipProcess;
use backend\models\EquipmentType;
use common\models\CoffeeProduct;
use Yii;
use yii\helpers\ArrayHelper;
use common\models\EquipProductGroupApi;

/**
 * This is the model class for table "equip_type_progress_product_assoc".
 *
 * @property integer $id
 * @property string $product_id
 * @property string $process_id
 * @property string $equip_type_is
 * @property string $enter_time
 * @property integer $enter_sort
 */
class EquipTypeProgressProductAssoc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $id;
    public $product_id; // 单品ID
    public $process_id; // 工序ID
    public $equip_type_id; // 设备类型ID
    public $enter_sort; // 输入顺序
    public $enter_time; // 输入时间
    
    public $product_name;
    public $process_name;
    public $equip_type_name;

    public $start_time; // 开始查询时间
    public $end_time;   // 结束查询时间
    public $isNewRecord;

    public $progress_bar_attributes; // 进度条(可读)属性
    public $equip_type_name_list;    // 所选的设备类型
    public $tableList;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id', 'process_id', 'equip_type_id', 'enter_time', 'enter_sort'], 'integer'],
            [['product_name', 'process_name', 'equip_type_name', 'equip_type_name_list','tableList', 'progress_bar_attributes'], 'safe'],
        ];
    }

    /**
     * 设备类型组件关联
     * @author  zmy
     * @version 2017-09-23
     * @return  [type]     [description]
     */
    public function getEquipType()
    {
        return $this->hasOne(EquipmentType::className(), ['equip_type_id' => 'equip_type_id']);
    }
    /**
     * 工序组件关联
     * @author  zmy
     * @version 2017-09-23
     * @return  [type]     [description]
     */
    public function getProcess()
    {
        return $this->hasOne(EquipProcess::className(), ['id' => 'process_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                      =>  'ID',
            'product_id'              =>  '产品名称',
            'process_id'              =>  '工序名称',
            'equip_type_id'           =>  '设备类型名称',
            'enter_time'              =>  '输入时间',
            'enter_sort'              =>  '输入顺序',

            'product_name'            =>  '产品名称',
            'process_name'            =>  '工序名称',
            'equip_type_name'         =>  '设备类型名称',
            'progress_bar_attributes' =>  '进度条可选属性',
            'equip_type_name_list'    =>  '设置的设备类型',
            
        ];
    }

    /**
     * 添加进度条
     * @author  zmy
     * @version 2017-09-07
     * @param   [Array]     $progressParams [进度条参数]
     * @return  [booblen]                   [true/false]
     */
    public static function saveEquipProcessBar($progressParams)
    {
        $proGressSaveSign = true;
        foreach ($progressParams['progressList'] as $key => $progressList) {
            $model                = new self();
            $model->product_id    = $progressParams['product_id'];
            $model->equip_type_id = $progressList['equip_type_id'];
            $model->process_id    = $progressList['process_id'];
            $model->enter_sort    = $progressList['enter_sort'];
            if(!$model->save()){
                $proGressSaveSign = false;
            }
        }
        if(!$proGressSaveSign) {
            return false;
        }
        return true;
    }
    
    /**
     * 根据进度条ID，查询进度条信息
     * @author  zmy
     * @version 2017-09-07
     * @param   [string]     $progressID [进度条ID]
     * @return  [Array]                  [进度条数组]
     */
    public static function getEquipProgressById($progressID)
    {
        $model                           =   self::findOne($progressID);
        $progressList                    =   [];
        $progressList['id']              =   $model->id;
        $progressList['product_id']      =   $model->product_id;
        $progressList['product_name']    =   isset($model->product->cf_product_name) ? $model->product->cf_product_name : "";

        $progressList['process_id']      =   $model->process_id;
        $progressList['process_name']    =   isset($model->process->process_name) ? $model->process->process_name : "";

        $progressList['equip_type_id']   =   $model->equip_type_id;
        $progressList['equip_type_name'] =   isset($model->equipType->equipment_name) ? $model->equipType->equipment_name : "";

        $progressList['enter_time']      =   $model->enter_time;
        $progressList['enter_sort']      =   $model->enter_sort;
        
        return $progressList;

    }

    /**
     * 根据进度条ID，删除进度条信息
     * @author  zmy
     * @version 2017-09-07
     * @param   [string]     $progressID [进度条ID]
     * @return  [boolen]                 [true/false]
     */
    public static function deleteEquipProgressById($progressID)
    {
        $model = self::findOne($progressID);
        return $model->delete();
    }

    /**
     * 获取已添加的进度条单品名称数组
     * @author  zmy
     * @version 2017-10-25
     * @return  [type]     [description]
     */
    public static function getProcessProductNameList()
    {
        $progressProductList = EquipProductGroupApi::getProgressProductList();
        $productNameList = [];
        foreach ($progressProductList as $key => $value) {
            $productNameList[] = $value['cf_product_name'];
        }
        return $productNameList;
    }

    /**
     * 组合进度条可选属性，获取table表格, 显示在页面
     * @author  zmy
     * @version 2017-09-11
     * @param   [Array]     $progressBarAttributeList [进度条可选属性]
     * @return  [string]                           [组合的div数据]
     */
    public function getProgressAttributes($progressBarAttributeList)
    {
        if(!$progressBarAttributeList){
            return '';
        }
        $progressBarStr = "<table class='table table-striped'>";
        foreach ($progressBarAttributeList as $key => $value) {
            $progressBarStr .= "<tr><td>".$value."</td></tr>";
        }
        $progressBarStr .= "</table>";
        return $progressBarStr;
    }

    /**
     * 组合进度条的设备类型，获取table表格, 显示在页面
     * @author  zmy
     * @version 2017-09-11
     * @param   [Array]     $equipTypeNameList [description]
     * @return  [string]                       [description]
     */
    public function getEquipTypeName($equipTypeNameList)
    {
        if (!$equipTypeNameList) {
            return '';
        }
        $equipTypeNameStr = "<table class='table table-striped'>";
        foreach ($equipTypeNameList as $key => $value) {
            $equipTypeNameStr .= "<tr><td>".$value."</td></tr>";
        }
        $equipTypeNameStr .= "</table>";
        return $equipTypeNameStr;
    }
    public function getequipTypeDetailsTable(){
        $html = '';
        if ($this->tableList) {
            $html .= '<table class="table table-bordered detail-view"><tbody>';
            $html .= '<tr><th>设备类型</th><th>工序名称</th><th>时间</th><th>排序</th></tr>';
            $list = ArrayHelper::index($this->tableList , null, 'equip_type_name');
            foreach($list as $equipTypeName => $item){
                $num = count($item);
                foreach($item as $key => $store){
                    $html .= '<tr>';
                    if($key == 0){
                        $html .= '<td rowspan="'.$num.'">'.$equipTypeName.'</td>';
                    }
                    $html .= '<td>'.$store['process_name'].'</td>';
                    $html .= '<td>'.$store['enter_time'].'</td>';
                    $html .= '<td>'.$store['enter_sort'].'</td>';
                    $html .= '</tr>';
                }
            }
            $html .= '</tbody></table>';
        }
        return $html;
    }
    
    

}
