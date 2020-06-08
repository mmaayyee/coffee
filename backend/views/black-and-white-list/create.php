<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\UserTag */

$this->title                   = '添加黑白名单';
$this->params['breadcrumbs'][] = ['label' => '黑白名单管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-tag-create">

    <h1><?=Html::encode($this->title)?></h1>
     <p>
        <?=!Yii::$app->user->can('添加黑白名单') ? '' : Html::a('输入添加', ['create?add_type=1'], ['class' => 'btn btn-success'])?>
        <?=!Yii::$app->user->can('添加黑白名单') ? '' : Html::a('导入添加', ['create?add_type=2'], ['class' => 'btn btn-success'])?>
    </p>

    <?=$this->render('_form', [
    'model' => $model,
])?>

</div>
