<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\LightBeltProductGroup */

$this->title = '添加饮品组';
$this->params['breadcrumbs'][] = ['label' => '饮品组管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="light-belt-product-group-create">

    <?= $this->render('_form', [
        'model' => $model,
        'productArr'	=>	$productArr,
    ]) ?>

</div>
