<?php

use backend\models\CoffeeLabel;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\CoffeeLabel */

$this->title                   = '查看产品标签';
$this->params['breadcrumbs'][] = ['label' => '产品标签列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coffee-label-view">

    <h1><?=Html::encode($this->title)?></h1>
<div class="">
    <table class="table table-responsive">
        <tbody>
            <tr>
                <td><label for="">标签名称</label></td>
                <td><?=$data['label_name']?></td>
            </tr>
            <tr>
                <td><label for="">上线状态</label></td>
                <td><?=$data['online_status'] == CoffeeLabel::ONLINE_UP ? '上线' : '下线'?></td>
            </tr>
            <tr>
                <td><label for="">标签类型</label></td>
                <td><?=$data['access_status'] == CoffeeLabel::ACCESS_DEFAULT ? '默认标签' : '非默认'?></td>
            </tr>
            <tr>
                <td><label for="">排序</label></td>
                <td><?=$data['sort']?></td>
            </tr>
            <tr>
                <td><label for="">桌面图(选中前)</label></td>
                <td><img style="width: 100px" src="<?=Yii::$app->params['fcoffeeUrl'] . $data['desk_img_url']?>"></td>
            </tr>
            <tr>
                <td><label for="">桌面图(选中后)</label></td>
                <td><img style="width: 100px" src="<?=Yii::$app->params['fcoffeeUrl'] . $data['desk_selected_img_url']?>"></td>
            </tr>
             <tr>
                <td><label for="">标签图</label></td>
                <td><img style="width: 100px" src="<?=Yii::$app->params['fcoffeeUrl'] . $data['label_img_url']?>"></td>
            </tr>
            <tr>
                <td><label for="">关联产品信息</label></td>
                <td><?=implode(',', $coffeeProductList)?></td>
            </tr>
        </tbody>
    </table>
</div>

</div>
