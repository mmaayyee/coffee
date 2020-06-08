<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\LightBeltProductGroup */

$this->title = '';
$this->params['breadcrumbs'][] = ['label' => '灯带策略管理', 'url' => ['index']];
$strategyList = isset($useStrategyList['strategy']) ? $useStrategyList['strategy'] : [];
$programList  = isset($useStrategyList['program']) ? $useStrategyList['program'] : [];
// echo "<pre/>";
// var_dump($programList);die();
?>

<div>
<?php if($strategyList){ ?>
	<table class="table table-striped">
		<tr>
			<td>使用场景</td>
		</tr>
		<?php foreach ($strategyList as $strategyID => $strategyName) { ?>
			<tr>
				<td><a href="/light-belt-scenario/view?id=<?php echo $strategyID ?>"><?php echo $strategyName; ?></a></td>
			</tr>
		<?php } ?>
	</table>
<?php } ?>
</div>

<div>
<?php if($strategyList){ ?>
	<table class="table table-striped">
		<tr>
			<td>使用方案</td>
		</tr>
		<?php foreach ($programList as $programID => $programName) { ?>
			<tr>
				<td><a href="/light-belt-program/view?id=<?php echo $programID ?>"><?php echo $programName; ?></a></td>
			</tr>
		<?php } ?>
	</table>
</div>
<?php } ?>
