<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/29
 * Time: 11:10
 */
use yii\helpers\Html;

$this->title = '蓝牙锁开门审核 ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '申请临时开门记录', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="temporary-authorization-update">
    <h1><?= Html::encode($this->title);?></h1>
    <?=$this->render('_form',['model'=>$model]); ?>
</div>
