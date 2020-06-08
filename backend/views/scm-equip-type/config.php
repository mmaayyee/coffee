
<?php
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '配置设备分类参数';
$this->params['breadcrumbs'][] = ['label' => '设备类型管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/bootstrap.min.css');
$this->registerJsFile('@web/js/jquery-2.0.0.min.js');
$this->registerJsFile('@web/js/equip-type-param-val.js');
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
  margin-left: 3rem;
}
.edit{
  margin-right: 1rem;
  margin-left: 1rem;
}
.delete{

}
.top{
  width: 10rem;
}
.bottom{
  width: 5rem;
}
</style>
<div class="scm-equip-type-index">

    <div class="equipmentType">
        <div>
            <span class="choose">选择地区 </span>
            <select class="select">
                <option value="0" selected="">全国</option>
                <?php foreach ($orgList as $org) {?>
                  <option value="<?php echo $org['org_id']; ?>"><?php echo $org['org_name']; ?></option>
                <?php }?>
            </select>
        </div>
    </div>
    <div class="allparameter" equipment_type_id='<?=$equipmentTypeId;?>'>
      <?php foreach ($paramValList as $key => $param) {?>
      <div class="parameter">
          <div class="add-row" parameter_id="<?=$param['id'];?>" max_parameter="<?=$param['max_parameter']?>" min_parameter="<?=$param['min_parameter']?>">
          <span><?php echo $param['parameter_name'] . '  (' . $param['min_parameter'] . '~' . $param['max_parameter'] . ')：'; ?></span>
          <input type="text" class="top" value="<?=$param['parameter_value'];?>" >
          <button class="btn btn-primary save" style="margin-bottom: 12px">同步</button>
          </div>
      </div>
    <?php }?>
  </div>
</div>
