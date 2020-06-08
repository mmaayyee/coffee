<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\BuildingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '零售活动人员二维码列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-build-assoc-index">
    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <p> <?=Yii::$app->user->can('生成零售活动人员二维码') ? Html::a('生成零售活动人员二维码', ['create'], ['class' => 'btn btn-success']) : '';?></p>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'label' => '姓名',
            'value' => function ($model) use ($searchModel) {return $model->getSaleName($searchModel->sale_arr, $model->sale_id);},
        ],
        [
            'label' => '楼宇名称',
            'value' => function ($model) use ($searchModel) {return $model->getBuildName($searchModel->build_arr, $model->build_id);},
        ],
        [
            'label' => '邮箱',
            'value' => function ($model) use ($searchModel) {return $model->getSaleField($searchModel->sale_list, $model->sale_id, 'sale_email');},
        ],
        [
            'label' => '手机号',
            'value' => function ($model) use ($searchModel) {return $model->getSaleField($searchModel->sale_list, $model->sale_id, 'sale_phone');},
        ],
        [
            'class'    => 'yii\grid\ActionColumn',
            'template' => '{delete} {upload}',
            'buttons'  => [
                'delete' => function ($url, $model) {
                    return Yii::$app->user->can('删除零售活动人员二维码') ? Html::a('', 'delete?id=' . $model->id . '&qrcode_img=' . $model->qrcode_img, ['onclick' => 'return confirm("确定删除吗？");', 'class' => 'glyphicon glyphicon-trash', 'title' => '删除']) : '';
                },
                'upload' => function ($url, $model) {
                    return Html::a('', 'upload?src=' . Url::to('@web/uploads/sale-register-qrcode/' . $model->qrcode_img, true), ['class' => 'glyphicon glyphicon-cloud-download', 'title' => '下载']);
                },
            ],
        ],
    ],
]);?>

</div>
