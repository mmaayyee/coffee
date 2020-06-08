<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ManagerLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '操作日志';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="manager-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
       // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                 'attribute' => 'realname',
                'format'=>'text',
                'value'=>function($model){return $model->manager->realname;}
            ],  
            'module_name',
            [
                 'attribute' => 'operate_type',
                'format'=>'text',
                'value'=>function($model){return $model->getType();}
            ],             
            'operate_content',
            [
                'attribute' => 'created_at',
                'format'=>'text',
                'value' => function ($model){ return date("Y-m-d H:i:s",$model->created_at);},
            ],  
        ],
    ]); ?>

</div>
