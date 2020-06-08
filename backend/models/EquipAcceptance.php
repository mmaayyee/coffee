<?php

namespace backend\models;
use common\models\Building;
use common\models\EquipTask;
use Yii;

/**
 * This is the model class for table "equip_acceptance".
 *
 * @property string $Id
 * @property string $build_id
 * @property string $reason
 * @property string $accept_time
 * @property string $accept_renson
 * @property integer $accept_result
 *
 * @property Building $build
 * @property EquipAcceptanceDebugAssoc[] $equipAcceptanceDebugAssocs
 * @property EquipAcceptanceLightBoxAssoc[] $equipAcceptanceLightBoxAssocs
 */
class EquipAcceptance extends \yii\db\ActiveRecord
{
    public $accept_lightbox_details;
    public $equip_lightbox_details;

    /**
    *   设备验收记录
    **/

    /** 全部未通过 */
    const ACCEPTANCE_FAIL = 0;
    /*设备通过；灯箱未通过*/
    const  LIGHTBOX_PASS=  1;
    
    /*灯箱通过；未通过*/
    const  EQUIP_PASS   =  2;

    /*通过*/
    const SUCCESS       =  3;

    //验收结果 1-灯箱通过；设备未通过 2-设备通过；灯箱未通过 3-通过
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'equip_acceptance';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['build_id', 'accept_time', 'accept_result', 'delivery_id'], 'integer'],
            [['accept_renson'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'build_id' => '楼宇名称',
            'accept_time' => '验收时间',
            'accept_renson' => '验收人员',
            'accept_result' => '验收结果',
            'accept_lightbox_details' => '灯箱验收详情',
            'equip_lightbox_details'  => '设备验收详情',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuild()
    {
        return $this->hasOne(Building::className(), ['id' => 'build_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(EquipTask::className(), ['relevant_id' => 'delivery_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipAcceptanceDebugAssocs()
    {
        return $this->hasMany(EquipAcceptanceDebugAssoc::className(), ['equip_acceptance_id' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipAcceptanceLightBoxAssocs()
    {
        return $this->hasMany(EquipAcceptanceLightBoxAssoc::className(), ['equip_acceptance_id' => 'Id']);
    }

    /**
    *   数组
    *   @return array $companyReasonArray
    *   验收结果 1-灯箱通过；设备未通过 2-设备通过；灯箱未通过 3-通过
    **/
    public function getAcceptResultArr(){
        $acceptResultArr = array(
            ''                      =>  '请选择',
            self::ACCEPTANCE_FAIL   => '全部未通过',
            self::EQUIP_PASS        =>  '灯箱通过；设备未通过',
            self::LIGHTBOX_PASS     =>  '设备通过；灯箱未通过',
            self::SUCCESS           => '全部通过',
            );
        return $acceptResultArr;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuildName()
    {
        return $this->hasOne(Building::className(), ['id' => 'build_id']);
    } 

    /**
     * 处理验收结果(投放验收时调用)
     * @author  zgw
     * @version 2016-09-08
     * @param   [type]     $params      [description]
     * @param   [type]     $buildId     [description]
     * @param   [type]     $userid      [description]
     * @param   [type]     $transaction [description]
     */
    public static function acceptanceResult($params, $deliveryModel, $userid) {
        $acceptanceModel = new EquipAcceptance();
        $acceptanceModel->build_id  =   $deliveryModel->build_id;
        $acceptanceModel->accept_renson = $userid;
        $acceptanceModel->accept_time   =   time();
        $acceptanceModel->sim_card      =   !empty($params['sim_card'])  ?   $params['sim_card'] :   '0';
        $acceptanceModel->sim_number    =   !empty($params['sim_number'])    ?   $params['sim_number']   :   '0';
        $acceptanceModel->app_number    =   !empty($params['app_number'])    ?   $params['app_number']   :   '';
        $acceptanceModel->big_app_number=   !empty($params['big_app_number'])    ?   $params['big_app_number']   :   '';

        $acceptanceModel->timer_model   =   !empty($params['timer_model'])    ?   $params['timer_model']   :   '';

        $acceptanceModel->leakage_circuit=  !empty($params['leakage_circuit'])   ?   $params['leakage_circuit']  :   '';
        $acceptanceModel->meter_model   =   !empty($params['meter_model'])   ?   $params['meter_model']  :   '';
        $acceptanceModel->power_value   =   !empty($params['power_value'])   ?   $params['power_value']  :   '0';
        // 设备调试项
        $debugIdArr         = EquipDebug::find()->select(['Id'])->where(['equip_type_id' => $deliveryModel->equip_type_id, 'is_del' => EquipDebug::DEL_NOT])->asArray()->all();
        $equipDebugArray= self::equipDebug($params, $debugIdArr);
        $acceptanceModel->debug_result  =   json_encode($equipDebugArray);
        if(in_array('false', $equipDebugArray)){
            $acceptResult = false;
        }else{
            $acceptResult = true;
        }
        if ($deliveryModel->is_lightbox > 0) { 
            //灯箱调试项
            $lightBoxDebugIdArr = EquipLightBoxDebug::find()->select(['Id'])->where(['light_box_id' => $deliveryModel->is_lightbox, 'is_del' => EquipLightBoxDebug::DEL_NOT])->asArray()->all();
            $lightBoxArray  =   self::equipLightBox($params, $lightBoxDebugIdArr);
            $acceptanceModel->light_box_result  =   json_encode($lightBoxArray);
            if(in_array('false', $lightBoxArray)){
                $lightAcceptResult = false;
            }else{
                $lightAcceptResult = true;
            }
            //按照条件返回不同的结果 accept_result
            $acceptanceModel->accept_result =   self::acceptResult($acceptResult, $lightAcceptResult);
        } else {
            $acceptanceModel->accept_result = $acceptResult ? self::SUCCESS : self::ACCEPTANCE_FAIL;
        }
        
        
        $acceptanceModel->delivery_id   =   $deliveryModel->Id;
        if ($acceptanceModel->save() === false) {
            Yii::$app->getSession()->setFlash('error', '验收结果数据录入失败');
            return false;
        }
        return true;
    }

    /**
     *  处理设备标记
     *  @param $params, $debugIdArr
     *  @return $equipDebugArray
     */
    public static function equipDebug($params, $debugIdArr){
        //设备
        $equipDebugArray = array();
        foreach ($debugIdArr as $debugK => $debugV) {
            if(isset($params['id']) && in_array($debugV['Id'], $params['id'])){
                $equipDebugArray[$debugV['Id']] = 'true';
            }else{
                $equipDebugArray[$debugV['Id']] = 'false';
            }
        }
        return $equipDebugArray;
    }

    /**
     *  处理灯箱标记
     *  @param $params, $lightBoxDebugIdArr
     *  @return $lightBoxArray
     */
    public static function equipLightBox($params, $lightBoxDebugIdArr){
        //灯箱
        $lightBoxArray = array();
        foreach ($lightBoxDebugIdArr as $lightBoxK => $lightBoxV) {
            if(isset($params['debug_item']) && in_array($lightBoxV['Id'], $params['debug_item'])){
                $lightBoxArray[$lightBoxV['Id']]  = 'true';
            }else{
                $lightBoxArray[$lightBoxV['Id']]  = 'false';
            }
        }
        return $lightBoxArray;
    }

    /**
     *  根据不同值，输出不同的结果
     *  @param $acceptResult, $lightAcceptResult
     *  @return $accept_result
     */
    public static function  acceptResult($acceptResult, $lightAcceptResult){
        if ($acceptResult==true && $lightAcceptResult==false) {
        $accept_result     =    EquipAcceptance::LIGHTBOX_PASS;  //设备通过；灯箱未通过 1;
        }else if ($acceptResult==false && $lightAcceptResult==true) {
            $accept_result =    EquipAcceptance::EQUIP_PASS;  //灯箱通过；设备未通过 2; 
        }else if($acceptResult && $lightAcceptResult){
            $accept_result =    EquipAcceptance::SUCCESS;  //全部通过 3; 
        }else{
            $accept_result =   0;
        }
        return $accept_result;
    }

}
