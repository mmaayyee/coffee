<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipmentTypeParameterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '设备类型参数管理';
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('@web/css/bootstrap.min.css');
$this->registerCssFile('@web/css/equipment-type-parameter.css');
$this->registerJsFile("/js/laytpl.js", ["depends" => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('@web/js/jquery-2.0.0.min.js');
$this->registerJsFile('@web/js/equipment-type-parameter.js');
?>
<script>
  var equipmentData = '<?php echo $equipmentTypeList;?>';
</script>
<div class="equipment-type-parameter-index">
  <div class="equipmentType"></div>
  <div class="parameter"></div>
</div>
<script id="taskListTpl" type="text/html">
  <div>
    <span class="choose">选择设备类型 </span>
    <select class="select">
      <option value="" selected>未选择</option>
      {{# $.each(JSON.parse(d),function(index,item){ }}
      <option value="{{ item.equip_type_id }}">{{ item.equipment_name }}</option>
      {{# }) }}
    </select>
  </div>
</script>



