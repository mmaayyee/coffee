<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\WxMember */

$this->title = '新建成员';
$this->params['breadcrumbs'][] = ['label' => '成员管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wx-member-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
