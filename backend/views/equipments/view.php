<?php

use backend\models\BuildingTaskSetting;
use backend\models\Organization;
use backend\models\ScmMaterialStock;
use common\models\Equipments;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $model common\models\Equipments */

$this->title                   = $model->id;
$this->params['breadcrumbs'][] = ['label' => '设备信息管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs('
    //ajax实现运营状态的修改功能
    $("#equipRunStatus").change(function(){
        var equipId = $(this).data("id"),
            operationStatus  = $(this).val();
        $.post(
            "/equipments/update-equip-run-status",
            {equipId:equipId, operationStatus:operationStatus},
            function(data){
                console.log(data);
                if(data !== "true"){
                    alert("操作失败");
                }
            }
        );
    });

    //ajax实现锁定、解锁、复位功能
    $("#isLock").change(function(){
        var equipId = $(this).data("id"),
            isLock  = $(this).val();
        $.post(
            "/equipments/equip-lock-status",
            {equipId:equipId, isLock:isLock},
            function(data){
                if(data !== "true"){
                    alert("操作失败");
                }
            }
        );
    });

    //ajax实现选择灯箱功能
    $("#lightBox").change(function(){
        var equipId = $(this).data("id"),
            lightBoxId  = $(this).val();
        $.post(
            "/equipments/change-light-box",
            {equipId:equipId, lightBoxId:lightBoxId},
            function(data){
                if(data !== "true"){
                    alert("操作失败");
                }
            }
        );
    });
')
;?>
<style>
	.equipments-view p a,.equipments-view p button{
		margin-bottom: 1%;
	}
</style>
<div class="equipments-view">

    <h1><?=Html::encode($this->title);?></h1>

    <p>
        <!-- 如果楼宇id不存在并且设备运营状态不为报废则可以执行绑定操作 -->
        <?php if (empty($model->build_id) && $model->operation_status != $model::SCRAPPED) {?>
            <?=Yii::$app->user->can('绑定') ? Html::button('绑定', ['class' => 'btn btn-primary', "data-toggle" => "modal", "data-target" => "#bind"]) : '';?>
        <?php }?>

        <?php if ($model->build_id) {?>

            <?=(Yii::$app->user->can('发起灯箱报修') && $model->light_box_id) ? Html::button('发起灯箱报修', ['id' => 'light_box_repair', 'data-id' => $model->build_id, 'class' => 'btn btn-primary', "data-toggle" => "modal", "data-target" => "#lightBoxRepair"]) : '';?>

            <?=Yii::$app->user->can('解绑') ? Html::button('解绑', ['id' => 'un_bind', 'data-id' => $model->build_id, 'class' => 'btn btn-primary', "data-toggle" => "modal", "data-target" => "#unBind"]) : '';?>


        <?php } else if ($model->operation_status == Equipments::PRE_SELIVERY) {?>

            <?=Yii::$app->user->can('报废') ? Html::a('报废', ['equipments/scrapped', 'id' => $model->id], ['class' => 'btn btn-primary', 'onclick' => 'return confirm("确定要报废设备吗?");']) : '';?>

        <?php }?>

        <?=!Yii::$app->user->can('配送任务记录') ? '' : Html::a('配送任务记录', ['distribution-task-record/index', 'DistributionTaskSearch[type]' => 1, 'DistributionTaskSearch[equip_id]' => $model->id], ['class' => 'btn btn-default']);?>


        <?=!Yii::$app->user->can('日志记录') ? '' : Html::a('日志记录', ['equip-log/index', 'EquipLogSearch[equip_code]' => $model->equip_code, 'equipId' => $model->id], ['class' => 'btn btn-default']);?>

            <?=!Yii::$app->user->can('设备报修记录') ? '' : Html::a('设备报修记录', ['equip-repair/index', 'EquipRepairSearch[equip_id]' => $model->id, 'EquipRepairSearch[type]' => 2], ['class' => 'btn btn-default']);?>

            <?=!Yii::$app->user->can('设备附件记录') ? '' : Html::a('设备附件记录', ['equip-extra-log/index', 'EquipExtraLogSearch[equip_id]' => $model->id], ['class' => 'btn btn-default']);?>

            <?=!Yii::$app->user->can('设备维修记录') ? '' : Html::a('设备维修记录', ['equip-task/index', 'EquipTaskSearch[equip_id]' => $model->id, 'EquipTaskSearch[type]' => 1], ['class' => 'btn btn-default']);?>

            <?=!Yii::$app->user->can('附件任务记录') ? '' : Html::a('附件任务记录', ['equip-task/index', 'EquipTaskSearch[equip_id]' => $model->id, 'EquipTaskSearch[type]' => 4], ['class' => 'btn btn-default']);?>

            <?=!Yii::$app->user->can('验收记录') ? '' : Html::a('验收记录', ['equip-check-delivery/index', 'equip_id' => $model->id], ['class' => 'btn btn-default']);?>

            <?=!Yii::$app->user->can('配送维修记录') ? '' : Html::a('配送维修记录', ['distribution-task-record/repair-task-record', 'DistributionTaskSearch[equip_id]' => $model->id], ['class' => 'btn btn-default']);?>

            <?=!Yii::$app->user->can('灯箱验收记录') ? '' : Html::a('灯箱验收记录', ['equip-task/index', 'EquipTaskSearch[equip_id]' => $model->id, 'EquipTaskSearch[type]' => 3], ['class' => 'btn btn-default']);?>

            <?=!Yii::$app->user->can('灯箱报修记录') ? '' : Html::a('灯箱报修记录', ['equip-light-box-repair/index', 'EquipLightBoxRepairSearch[equip_id]' => $model->id], ['class' => 'btn btn-default']);?>
            <?=!Yii::$app->user->can('配方调整') ? '' : Html::a('配方调整', ['equipments/formula-adjustment', 'equip_code' => $model->equip_code, 'equipTypeId' => $model->equip_type_id], ['class' => 'btn btn-default']);?>
            <?=!Yii::$app->user->can('设备参数配置') ? '' : Html::a('设备参数配置', ['equipments/config?id=' . $model->id . '&equipmentsCode=' . $model->equip_code], ['class' => 'btn btn-default']);?>
            <!-- 发起灯箱报修 -->
            <?=$this->render('light_box_repair_form', ['lightBoxRepairModel' => $lightBoxRepairModel, 'build_id' => $model->build_id, 'equip_id' => $model->id]);?>

            <!-- 解绑操作，选择分库 -->
            <?=$this->render('un_bind', ['scmWarehouse' => $scmWarehouse, 'build_id' => $model->build_id, "equip_id" => $model->id]);?>
            <!-- 绑定操作 -->
            <?=$this->render('bind', ['equipModel' => $model]);?>

    </p>

    <?=DetailView::widget([
    'model'      => $model,
    'attributes' => [
        [
            'attribute' => 'equip_type_id',
            'format'    => 'html',
            'value'     => !isset($model->equipTypeModel) ? '' : $model->equipTypeModel->model,
        ],
        [
            'attribute' => 'pro_group_id',
            'value'     => $model->pro_group_id ? $model->proGroupList($model->pro_group_id) : '',
        ],
        'equip_code',
        [
            'attribute' => 'warehouse_id',
            'value'     => $model->getWarehouseNameById($model->warehouse_id),
        ],
        [
            'attribute' => 'org_id',
            'value'     => Organization::getField('org_name', array('org_id' => $model->org_id)),
        ],
        [
            'attribute' => 'build_id',
            'format'    => 'html',
            'value'     => !empty($model->build) ? Html::a($model->build->name, ['/building/view?id=' . $model->build_id]) : '',
        ],
        [
            'label'  => '设备二维码',
            'format' => ['image', ['width' => '100', 'height' => '100']],
            'value'  => isset($model->equip_code) ? \common\models\Api::buildCode($model->equip_code) : '',
        ],
        [
            'attribute' => 'equipment_status',
            'value'     => $model->equipStatusArray()[$model->equipment_status],
        ],
        [
            'attribute' => 'operation_status',
            'format'    => 'raw',
            'value'     => ($model->build_id && Yii::$app->user->can('修改运营状态')) ? Html::dropDownList('修改运营状态', $model->operation_status, Equipments::operationStatusByConditionsArray(2), ['id' => 'equipRunStatus', 'data-id' => $model->id, 'class' => 'form-control']) : $model->operationStatusArray()[$model->operation_status],
        ],
        [
            'attribute' => 'is_lock',
            'format'    => 'raw',
            'value'     => ($model->build_id && Yii::$app->user->can('锁定设备')) ? Html::dropDownList('锁定设备', $model->is_lock, $model::$changeLock, ['id' => 'isLock', 'data-id' => $model->id, 'class' => 'form-control']) : $model::$lock[$model->is_lock],

        ],
        [
            'attribute' => 'light_box_id',
            'format'    => 'raw',
            'value'     => $model->changeLightBox(),
        ],
        [
            'attribute' => 'create_time',
            'value'     => !empty($model->create_time) ? date('Y-m-d H:i:s', $model->create_time) : '暂无',
        ],
        [
            'attribute' => 'equip_operation_time',
            'value'     => !empty($model->equip_operation_time) ? date('Y-m-d H:i:s', $model->equip_operation_time) : '暂无',
        ],

        'factory_equip_model',
        'last_log',
        [
            'attribute' => 'last_update',
            'value'     => $model->last_update ? date('Y-m-d H:i:s', $model->last_update) : '暂无',
        ],
        [
            'attribute' => 'factory_code',
            'value'     => !empty($model->factory_code) ? $model->factory_code : '暂无',
        ],
        [
            'attribute' => 'card_number',
            'value'     => !empty($model->card_number) ? $model->card_number : '暂无',
        ],
        [
            'attribute' => 'miscellaneou_remark',
            'value'     => !empty($model->miscellaneou_remark) ? $model->miscellaneou_remark : '暂无',
        ],
        [
            'attribute' => 'concentration',
            'value'     => !empty($model->concentration) ? $model->concentration : '暂无',
        ],
        'batch',
        [
            'attribute' => 'cleaningCycle',
            'value'     => $model->cleaningCycle . '天',
        ],
        [
            'attribute' => 'refuelCycle',
            'value'     => BuildingTaskSetting::getRuelCycle($model->refuelCycle),
        ],
        [
            'attribute' => 'dayNum',
            'value'     => $model->dayNum . '天',
        ],
        [
            'attribute' => 'bluetooth_name',
            'value'     => function ($model) {
                return $model->bluetooth_name ? $model->bluetooth_name : '';
            },
        ],
        [
            'attribute' => '楼宇经度',
            'value'     => function ($model) {
                return !empty($model->build->longitude) ? $model->build->longitude : '';
            },
        ],
        [
            'attribute' => '楼宇维度',
            'value'     => function ($model) {
                return !empty($model->build->latitude) ? $model->build->latitude : '';
            },
        ],
        [
            'attribute' => 'equipment_longitude',
            'value'     => function ($model) {
                return $model->equipment_longitude ? $model->equipment_longitude : '';
            },
        ],
        [
            'attribute' => 'equipment_latitude',
            'value'     => function ($model) {
                return $model->equipment_latitude ? $model->equipment_latitude : '';
            },
        ],
        [
            'attribute' => 'specific_position',
            'value'     => function ($model) {
                return $model->specific_position ? $model->specific_position : '';
            },
        ],
        [
            'attribute' => '设备回传位置与楼宇位置距离',
            'value'     => function ($model) {
                if (!empty($model->build)) {
                    $from = $model->equipment_latitude . "," . $model->equipment_longitude;
                    $to   = $model->build->latitude . "," . $model->build->longitude;
                    return $model->difference($from, $to) . '米';
                }
                return '';
            },
        ],
        [
            'attribute' => 'building_location_time',
            'value'     => function ($model) {
                return !empty($model->build) && $model->building_location_time ? date('Y-m-d H:i:s', $model->building_location_time) : '';
            },
        ],
    ],
]);?>

    <h1>料仓剩余物料：</h1>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => '设备编号',
            'value'     => function ($model) {
                return $model->equip_code;
            },
        ],
        [
            'attribute' => '料仓编号',
            'value'     => function ($model) {
                return !empty($model->stock_code) ? ScmMaterialStock::getMaterialStockDetail('*', array('stock_code' => $model->stock_code))["name"] : '暂无';
            },
        ],
        [
            'attribute' => '预警值',
            'value'     => function ($model) {
                // return sprintf("%.0f", $model->warning_value);
                return round($model->warning_value);
            },
        ],
        [
            'attribute' => '上限值',
            'value'     => function ($model) {
                return round($model->stock_volume_bound);
            },
        ],
        [
            'attribute' => '剩余物料',
            'value'     => function ($model) {
                return round($model->surplus_material);
            },
        ],
        [
            'attribute' => '下限值',
            'value'     => function ($model) {
                return round($model->bottom_value);
            },
        ],
        [
            'attribute' => '时间',
            'value'     => function ($model) {
                return $model->date ? date("Y-m-d H:i:s", $model->date) : '暂无';
            },
        ],
    ],
]);?>

</div>
