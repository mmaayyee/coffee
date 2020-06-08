<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\LightBeltProductGroup */

$this->title = '';
$this->params['breadcrumbs'][] = ['label' => '饮品组管理', 'url' => ['index']];
?>

<div>
	<table class="table table-striped">
		<tr>
			<td>场景名称</td>
		</tr>
		<?php foreach ($useScenarioList as $scenarioID => $scenarioName) { ?>
			<tr>
				<td><a href="/light-belt-scenario/view?id=<?php echo $scenarioID ?>"><?php echo $scenarioName; ?></a></td>
			</tr>
		<?php } ?>
	</table>
</div>
