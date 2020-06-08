<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipRfidCardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '轻食产品上下架管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equip-rfid-card-index">
	<?php $form = ActiveForm::begin(['method' => 'post']);?>
    <div class="form-group">
        <label class="control-label" for="productofflinerecord-type">请选择上下架状态</label>
        <select class="form-control" name="productStatus" >
            <option value="0">上架</option>
            <option value="1">下架</option>
        </select>
        <div class="help-block"></div>
    </div>
    <?=Html::checkboxList('productIdList', '', $productList)?>
    <div class="form-group">
        <?=Html::submitbutton('提交', ['class' => 'btn btn-primary generate'])?>
    </div>
    <?php ActiveForm::end();?>
</div>
