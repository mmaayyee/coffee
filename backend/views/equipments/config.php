
<?php
use backend\models\EquipRfidCard;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '配置参数';
$this->params['breadcrumbs'][] = ['label' => '设备详情', 'url' => ['view', 'id' => Yii::$app->request->get('id')]];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/bootstrap.min.css');
$this->registerJsFile('@web/js/jquery-2.0.0.min.js');
$this->registerJsFile('@web/js/equip-param-val.js');
?>
<style>
html{
  font-size: 14px;
}
.equipmentType{
  padding: 1rem;
}
.select{
  width: 14.5rem;
}
option{
  height: 3rem;
}
.add-row input{
  height: 2.5rem;
}
.add-row span{
  display: inline-block;
  padding: 1rem;
  width: 300px;
  text-align: right;
}
.save{
  margin-left: 10rem;
}
.edit{
  margin-right: 1rem;
  margin-left: 1rem;
}
.top{
  width: 10rem;
}
.bottom{
  width: 5rem;
}
.allparameter{
    float:left;
}
.log{
  background-color: #fff;
  border: 1px solid transparent;
  margin-left:100px;
  width:250px;
  float:left;
  border-radius: 4px;
  -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
  box-shadow: 0 1px 1px rgba(0,0,0,.05);
}
.log div{
  margin-bottom: 1rem;
}
.log span{
  display: inline-block;
  padding: 5px;
}
.log .see_log{
  margin-left:5px
}
</style>
<div class="scm-equip-type-index">
    <div class="allparameter" equipments_code='<?=$equipmentsCode;?>'>
	    <?php
if ($paramValList) {
    foreach ($paramValList as $key => $param) {
        ?>
	    <div class="parameter">
	        <div class="add-row" parameter_id="<?=$param['id'];?>" max_parameter="<?=$param['max_parameter']?>" min_parameter="<?=$param['min_parameter']?>">
	        <span><?php echo $param['parameter_name'] . '  (' . $param['min_parameter'] . '~' . $param['max_parameter'] . ')：'; ?></span>
	        <input type="text" class="top" value="<?=$param['parameter_value'];?>" >
	        </div>
	    </div>
		  <?php
}?>
      <button class="btn btn-primary save" style="margin-bottom: 12px">确认修改</button>
      <?php } else {?>

      <?php echo '请到设备类型参数管理中新增参数';} ?>
	</div>
</div>
