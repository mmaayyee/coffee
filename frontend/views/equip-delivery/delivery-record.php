<?php  
use common\models\Building;
use backend\models\EquipDelivery;
use yii\helpers\Url;
$deliveryModel = new EquipDelivery();

$this->title = '投放验收记录';
?>

<div class="panel panel-default">
   <div class="panel-heading">
      <h3 class="panel-title" style="text-align:center;margin: 0 auto;">
		投放验收记录
      <span class="badge"><?php echo $taskCount; ?></span>
      </h3>
   </div>
   <div class="panel-body">
     <?php foreach ($equipTaskArr as $key => $value) { ?>
      <div class="row" >
         <div class="col-xs-4" >
            <?php $model = EquipDelivery::findOne([$value['relevant_id']]); if($model->delivery_status==EquipDelivery::DELIVERY_FAILURE){ ?>
               <p style="color:#e4393c;font-weight: bold;">
                  <?php if($model){
                     echo $model->equipDeliveryStatusArray()[$model->delivery_status];
                  }else{continue;} ?>
               </p>
            <?php }else{ ?>
            	<p>
                  <?php if($model){
                     echo $model->equipDeliveryStatusArray()[$model->delivery_status];
                  }else{continue;} ?>
               </p>
            <?php }?>
         </div>

         <a href="<?php echo Url::to(['equip-delivery/delivery-info','delivery_id'=>$value['relevant_id'], 'recive_time'=>$value['recive_time'], 'end_repair_time'=>$value['end_repair_time'] ]) ?>">
         <div class="col-xs-8" >
            <div class="row">
               <div class="col-xs-12" >
                  <p><?php echo Building::getBuildingDetail('name', ['id'=>$value['build_id']])['name']; ?></p>
               </div>
               <div class="col-xs-12" >
                  <p><?php echo date("Y年m月d日 H时i分s秒", $value['recive_time']) ?>---->>><?php echo date("d日 H时i分s秒", $value['end_repair_time']) ?></p>
               </div>
            </div>
         </div>
         </a>
      </div>
      <hr/>
	<?php  } ?>
   </div>
</div>

