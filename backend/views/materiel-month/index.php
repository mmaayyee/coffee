<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\MaterielMonthSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '差异值';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="materiel-month-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?=$this->render('_search', ['model' => $searchModel]);?>
     <div id="w1" class="grid-view"><div class="summary">第<b><?=$searchModel->page * 20 + 1?>-<?=$searchModel->page * 20 + 20?></b>条，共<b><?= isset($dataProvider['total']) ? $dataProvider['total'] : 0;?></b>条数据.</div>
         <?php if (!empty($dataProvider)) : ?>
         <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>楼宇名称</th>
                    <th>月份</th>
                    <?php foreach ($dataProvider['materialTypeName'] as $k => $v): ?>
                        <th><?=$v?></th>
                    <?php endforeach;?>
                    <th>地区</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($dataProvider['materielMonthList'])): ?>
                    <?php $i = 1;foreach ($dataProvider['materielMonthList'] as $k => $v): ?>
                        <tr data-key=<?=$k?> >
                            <td><?=$i?></td>
                            <td><?=$v['build_name']?></td>
                            <td><?=date('Y年m月', $v['time'])?></td>
                                <?php foreach ($v['info'] as $key => $val): ?>
                                    <th><?=$val?></th>
                                <?php endforeach;?>
                            <td><?=$v['orgName']?></td>
                            <td>
                                <?php if (Yii::$app->user->can('物料消耗差异值编辑') && (date('Ym', $v['time']) == date('Ym', strtotime(' -1 month ')))): ?>
                                <a href="/materiel-month/update?buildId=<?=$v['buildId']?>&createAt=<?=$v['time']?>">修改</a>
                                <?php endif;?>
                            </td>
                        </tr>
                    <?php $i++;endforeach;?>
                <?php else: ?>
                    <tr><td colspan="<?php echo count($dataProvider['materialTypeName']) + 5 ?>">没有符合条件的数据</td></tr>
                <?php endif;?>
            </tbody>
        </table>
         <?php else : ?>
         <table class="table table-striped table-bordered">
             <thead>
             <tr>
                 <th>#</th>
                 <th>楼宇名称</th>
                 <th>月份</th>
                 <th>地区</th>
                 <th>操作</th>
             </tr>
             </thead>
             <tbody>
             <tr><td colspan="<?php echo count(isset($dataProvider['materialTypeName']) ? $dataProvider['materialTypeName'] : 0) + 5 ?>">暂无数据</td></tr>
             </tbody>
         </table>
         <?php endif; ?>
    </div>
    <?php
echo LinkPager::widget([
    'pagination' => $pages,
])
?>

</div>
