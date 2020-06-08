<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\ScmEquipType;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\AppVersionManagementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'App版本号管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="app-version-management-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'big_screen_version',
            'small_screen_version',
            [
                'attribute' => 'equip_type_id',
                'value' => function($model) {
                    return $model->equip_type_id ? ScmEquipType::getEquipTypeDetail("*", ['id'=>$model->equip_type_id])['model'] : '';
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{view}{update}',
                'buttons'=>[
                    'view' => function($url) {
                        return Yii::$app->user->can('查看App版本号') ? Html::a('', $url, ['class' => 'glyphicon glyphicon-eye-open', 'title' => '查看']) : '';
                    },
                    'update' => function ($url, $model) {
                        return Yii::$app->user->can('编辑App版本号') ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url) : '';
                    }
                ]
            ],
        ],
    ]); ?>
</div>
