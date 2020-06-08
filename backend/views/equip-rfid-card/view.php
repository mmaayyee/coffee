<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\EquipRfidCard;
use backend\models\EquipRfidCardAssoc;
use backend\models\Organization;
/* @var $this yii\web\View */
/* @var $model backend\models\EquipRfidCard */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'RFID卡管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
	#responsible li,#binding li{
		display: inline-block;
		width:49%;
	}
	.panel-heading{
		font-weight: bold;
		cursor:pointer ;
	}
	#responsible li:nth-child(1),#responsible li:nth-child(2),#binding li:nth-child(1),#binding li:nth-child(2){
		margin-top: 15px;
	}
	#responsible li:nth-child(odd),#binding li:nth-child(odd){
		padding-left:15px ;
	}
	#binding li:nth-last-child(2),#binding li:nth-last-child(2){
		padding-left:15px ;
	}
	.panel-default{
		padding-bottom:15px ;
	}
</style>
<div class="equip-rfid-card-view">
    <p>
        <?=Yii::$app->user->can('编辑RFID门禁卡') ? Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) : ""?>
        <?=Yii::$app->user->can('删除RFID门禁卡') ? Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '确定删除吗?',
                //'method' => 'post',
            ],
        ]) : "" ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'rfid_card_code',
            [
                'attribute' => 'member_id',
                'value' => isset($model->member->name) ? $model->member->name : "",
            ],
            [
                'attribute' => 'area_type',
                'value' => $model->area_type ? EquipRfidCard::$areaType[$model->area_type] : "",
            ],
            [
                'attribute' => 'org_id',
                'format'    => 'html',
                'value'     =>  $model->getOrgName($model->org_id),
            ],
            [
                'attribute' => 'create_time',
                'value' => !empty($model->create_time) ? date('Y-m-d H:i', $model->create_time) : '',
            ],
            [
                'attribute' => 'rfid_state',
                'value' => EquipRfidCard::$rfidState[$model->rfid_state],
            ],
            [
                 'attribute' => 'is_bluetooth',
                 'value' => $model->is_bluetooth ? '禁止' : '授权',
            ],
        ],
    ]) ?>

</div>

<ul class="list-unstyled" id="building" aria-multiselectable="true">  
<?php if($model->area_type == EquipRfidCard::BRANCH_PART || $model->area_type == EquipRfidCard::COUNTRY_PART){ ?>
    	 <li class="panel panel-default">
        <?php  $equipCodeArrByCodeArr = EquipRfidCardAssoc::getEquipCodeArrByCode($model->rfid_card_code); if($equipCodeArrByCodeArr){?>
            <div data-toggle="collapse" data-target="#responsible" class="panel-heading ">负责楼宇</div>
            <ul id="responsible" class="list-unstyled collapse in ">      
        <?php foreach ( $equipCodeArrByCodeArr as $key => $value) { ?>
                	<li><?php echo $value; ?></li>
        <?php } }?>
            </ul>
        </li>
         <li class="panel panel-default">
    <?php $equipCodeArrByCodeOffArr = EquipRfidCardAssoc::getEquipCodeArrByCodeOff($model->rfid_card_code); if($equipCodeArrByCodeOffArr){ ?>
        	<div data-toggle="collapse"  data-target="#binding" class="panel-heading ">绑定楼宇(离线可开门)</div>
            <ul id="binding" class="list-unstyled collapse in">     
        <?php foreach ($equipCodeArrByCodeOffArr as $key => $value) { ?>
            
                  <li><?php echo $value; ?></li> 
              
        <?php } }?>
			 </ul>
        </li>
<?php } ?>
 </ul>

