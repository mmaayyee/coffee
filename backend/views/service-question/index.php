<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ServiceQuestionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '话术管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-question-index">

    <h1><?=Html::encode($this->title)?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel, 'category' => $category]); ?>

    <?=GridView::widget([
	'dataProvider' => $dataProvider,
	'columns' => [
		[
			'class' => 'yii\grid\CheckboxColumn',
			'header' => '序号',
			'checkboxOptions' => function ($model) {
				return ["value" => $model['id']];
			},
		],
		[
			'label' => '问题',
			'attribute' => 'question',
		],
		[
			'label' => '关键词',
			'format' => 'text',
			'value' => function ($model) {
				return $model->getKeys($model->id);
			},
		],
		[
			'label' => '类别',
			'format' => 'text',
			'value' => function ($model) {
				return $model->getQuestionCategoryQuestionID($model->s_c_id);
			},
		],
		[
			'label' => '状态',
			'format' => 'html',
			'value' => function ($model) {
				return '<span class="status">' . $model->getStatus($model->static) . '</span>';
			},
		],
		[
			'label' => '创建时间',
			'value' => function ($model) {
				return date('Y-m-d', $model->create_time);
			},
		],
	],
]);?>
</div>
