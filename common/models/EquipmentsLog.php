<?php

namespace common\models;

use Yii;
use yii\db\Connection;
use backend\models\EquipmentVolume;


/**
 * This is the model class for table "equipments_log".
 *
 * @property integer $equipment_log_id
 * @property string $equipment_code
 * @property string $log_content
 * @property integer $log_type
 * @property integer $log_status
 * @property integer $created_at
 *
 * @property Equipments $equipmentCode
 */
class EquipmentsLog extends \yii\db\ActiveRecord
{
    
     /*
     * 正常
     */
    const  IS_WORK = 0;
    
     /*
     * 故障
     */
    const  IS_ERROR = 1;
    
     /*
     * 缺料
     */
    const  IS_LACK = 2;
    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equipments_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['log_content', 'created_at'], 'required'],
            [['log_type', 'log_status', 'created_at'], 'integer'],
            [['equipment_code'], 'string', 'max' => 50],
            [['log_content'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'equipment_log_id' => '设备日志ID',
            'equipment_code' => '设备编号',
            'log_content' => '日志内容',
            'log_type' => '日志类型',
            'log_status' => '设备状态',
            'created_at' => '上报时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipmentCode()
    {
        return $this->hasOne(Equipments::className(), ['equipment_code' => 'equipment_code']);
    }
    
    /**
     * 获取设备日志状态
     * @return string 设备日志状态
     */
    public function getStatus(){
        $statusArray = $this->getStatusArray();
        return $statusArray[$this->log_status];
    }
    
    /**
     * 获取设备日志状态数组
     * @return array 设备日志状态数组
     */
    public function getStatusArray(){
        return array(
            '0'=>'正常',
            '1'=>'不正常'
        );
    }    
    
    /**
     * 获取设备日志状态
     * @return string 设备日志状态
     */
    public function getType(){
        $statusArray = $this->getTypeArray();
        return $statusArray[$this->log_type];
    }
    
    /**
     * 获取设备日志类型数组
     * @return array 设备日志类型数组
     */
    public function getTypeArray(){
        return array(
            '0'=>'正常',
            '1'=>'不正常',
            '2'=>'缺料',
        );
    }    
    
    /**
     * 保存健康数据
     * @param array $logData 日志数组
     * @param int $logType 日志类型
     * @return boolean 保存成功
     */
    
    public static function saveLog($logData, $logType){
            $db = Yii::$app->db;
            $equipCode = $logData->equipment;
            $equipStatus = $logData->status;
            if(isset($logData->log))            
                $logList = $logData->log;  
            else {
                $logList = array();
            }
            if(isset($logData->volume))
                $volumeList = $logData->volume;
            else {
                $volumeList = array();
            }
            $transaction = $db->beginTransaction();
            try {
                //更新设备状态
                $equipment = Equipments::findOne(['equipment_code'=>$equipCode]);
                $equipment->status = $equipStatus;
                $equipment->last_update = time();
                $equipment->last_log = time();
                
                //保存汇报日志
                  foreach($logList as $log){
                      $model = new EquipmentsLog();
                      $model->equipment_code = $equipCode;
                      $model->log_content = $log[0];
                      $model->log_type = $logType;
                      $model->log_status = $equipStatus;
                      $model->created_at = time();;
                      $model->save();
                      $equipment->last_log = $log[0];
                  }
                  //设备保存
                  $equipment->save();

                //保存料仓剩余量
                  foreach($volumeList as $key=>$volume){
                      $model = EquipmentVolume::findOne(['equipment_id'=>$equipment->equipment_id, 'stock_code'=>$key]);
                      if($model){
                        $model->volume = $volume;
                        $model->update_at = time();
                        $model->save();
                      }else{
                        $model = new EquipmentVolume();
                        $model->stock_code = $key;
                        $model->equipment_id = $equipment->equipment_id;                        
                        $model->volume = $volume;
                        $model->update_at = time();
                        $model->save();                          
                      }
                  }                  
                  $transaction->commit();
                  return true;
              } catch(\Exception $e) {
                  $transaction->rollBack();
                  return false;
              }
    }
    
}
