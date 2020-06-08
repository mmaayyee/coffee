<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/26
 * Time: 17:24
 */
use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\TemporaryAuthorization;

$this->title = '申请临时开门记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="temporary-authorization-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_search',['model' => $searchModel]);?>
    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns'  => [
                ['class' => 'yii\grid\SerialColumn'],
                'build_name',
                'wx_member_name',
                [
                    'attribute' => 'application_time',
                    'value'      => function ($model){
                        return $model->application_time ? date("Y-m-d H:i:s", $model->application_time) : '';
                    }
                ],
                [
                        'attribute' => 'audit_time',
                        'value'      => function ($model) {
                            return $model->audit_time ? date("Y-m-d H:i:s", $model->audit_time) : '';
                        }
                ],
                [
                        'attribute' => 'state',
                        'value'     => function ($model) {
                            return $model->getStatusName();
                        }
                ],
                [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update}',
                        'buttons' => [
                                'view' => function ($url, $model, $key) {
                                        return !\Yii::$app->user->can('编辑蓝牙锁') ? '' : Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url);
                                },
                                 'update' => function ($url, $model, $key) {
                                         return $model->isCanUpdate() ? '' : Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url);
                                 }
                        ],
                ],
            ]
    ]); ?>
</div>
