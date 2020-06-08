<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ServiceCountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '统计管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-count-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel,'category' =>$category]); ?>
    <?php if (Yii::$app->user->can('Excel导出')) : ?>
    <?= Html::a('Excel导出', ['/service-count/excel-export', 'list'=> isset($list)?$list:""], ['class' => 'btn btn-success btn-right-param']) ?>
     <?php endif ?>

    <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <?php foreach($list[0] as $date){
                   echo "<th>$date</th>";
                }?>
            </tr>
            </thead>
            <tbody>
            <?php foreach($list as $key=>$countList){
                if ($key==0){
                    continue;
                }
                echo "<tr>";
                foreach ($countList as $count) {
                    echo "<th>{$count}</th>";
                }
                echo "</tr>";
            }?>
            </tbody>
        </table>
</div>
<?php 
echo LinkPager::widget([
    'pagination' => $pages,
])
?>
