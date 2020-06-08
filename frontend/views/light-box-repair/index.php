<?php 
use common\models\EquipLightBoxRepair;
$this->title = "灯箱报修";
?>
<div class="panel panel-default">
   <div class="panel-heading">
      <h3 class="panel-title" style="text-align:center;margin: 0 auto;">
         <?php  echo $type < 8 ? '需要维修' : '维修记录'; ?>   
         <span class="badge"><?php echo $task_count; ?></span>
      </h3>
   </div>
   <div class="panel-body">
      <?php foreach ($task_list as $key => $value) { ?>
      <div class="row" >
         <div class="col-xs-4" >
         <?php echo EquipLightBoxRepair::$process_result[$value->process_result];?>
         </div>
         <a class="read" data-type="<?php echo $value->process_result; ?>" data-id="<?php echo $value->id; ?>" href="/light-box-repair/detail?id=<?php echo $value->id; ?>">
         <div class="col-xs-8" >
            <div class="row">
               <div class="col-xs-12" >
                  <p><?php echo $value->build? $value->build->name : ''; ?></p>
               </div>
               <div class="col-xs-12" >
                  <p><?php echo $value->create_time ? date('Y年m月d日 H点i分',$value->create_time) : ''?></p>
               </div>
            </div>
         </div>
         </a>
      </div>
      <hr/>
      <?php } ?>
   </div>
</div>
<?php
   $this->registerJs('
      $(".read").click(function(){
         if ($(this).data("type") == 1){
            $.get("change-process-result",{id:$(this).data("id"), process_result:2});
         }
      });
   ');
?>