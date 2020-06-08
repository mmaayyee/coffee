<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\LightBeltProductGroup */

$this->title = '';
$this->params['breadcrumbs'][] = ['label' => '灯带场景管理', 'url' => ['index']];
?>

<div>
	<table class="table table-striped">
		<tr>
			<td>使用方案</td>
		</tr>
		<?php foreach ($useScenarioList as $scenarioID => $scenarioName) { ?>
			<tr>
				<td><a href="/light-belt-program/view?id=<?php echo $scenarioID ?>"><?php echo $scenarioName; ?></a></td>
			</tr>
		<?php } ?>
	</table>
</div>
