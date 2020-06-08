<?php
$this->title = "灯箱验收";
?>
<style>
   .panel-default > .panel-heading {
      text-align: center;
         display: table;
         width:100%;
   }
    .panel-heading h3{
       width: 60%;
        }
   #search_r{
      display: table-cell;
      vertical-align: middle;
      font-size: 1.6rem;
      width: 8%;
   }
</style>
<div class="panel panel-default">
   <div class="panel-heading">
      <h3 class="panel-title" style="text-align:center;margin: 0 auto;">

      <?php if ($process_result == 1) {
    echo '需要灯箱验收';
    $action = '/equip-task/detail';} else {
    echo '灯箱验收记录';
    $action = '/equip-task/acceptance-record-detail';}?>
      <span class="badge"><?php echo $task_count; ?></span>
      </h3>
      <div  id="search_r" >
         <a href="/equip-task/index?task_type=3&process_result=<?php echo $process_result; ?>">
             <span class="glyphicon glyphicon-repeat"></span>
         </a>
     </div>
   </div>
   <div class="panel-body">
      <?php foreach ($task_list as $key => $value) {
    ?>
      <div class="row" >
         <div class="col-xs-4" >
         <?php if ($value['process_result'] == 1) {
        echo "<p>灯箱验收</p>";
    } else if ($value['process_result'] == 2) {
        echo "<p>验收通过</p>";
    } else {
        echo "<p style='color:#e4393c;font-weight: bold;'>验收未通过</p>";
    }?>
         </div>

         <a href="<?php echo $action . '?id=' . $value['id']; ?>">
         <div class="col-xs-8" >
            <div class="row">
               <div class="col-xs-12" >
                  <p><?php echo $value['build']['name']; ?></p>
               </div>
               <div class="col-xs-12" >
                  <p><?php echo $value['create_time'] ? date('Y年m月d日 H点i分', $value['create_time']) : '' ?></p>
               </div>
            </div>
         </div>
         </a>
      </div>
      <hr/>
      <?php }?>
   </div>
</div>
