<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\controllers\ScmWarehouseOutSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '出库单';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs('
');
?>
<div class="scm-warehouse-out-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>领料人</th>
            <th>领料时间</th>
            <th width="60%">出库物料及包数</th>
            <th>领料仓库</th>
            <th>确认领料</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($packetArr as $date => $packets) : ?>

            <?php foreach ($packets as $author => $statusArr) : ?>

                <?php foreach ($statusArr as $status => $packet) : ?>
                    <tr>
                        <td><?php echo $packet['distribution_user_name']; ?></td>
                        <td><?php echo $date; ?></td>
                        <td class="task-material">
                            <?php foreach ($packet['data'] as $material): ?>
                                <?php if(array_sum($material['material_out_num']) > 0 || array_sum($material['material_out_gram']) > 0):?>
                                <?php echo $material['material_name'] . ' ' . array_sum($material['material_out_num']) . ' ' . $material['unit'] . ' '; ?>
                                <?php echo array_sum($material['material_out_gram']) > 0 ? '  --  散料: ' . array_sum($material['material_out_gram']) . ' ' . $material['weight_unit'] . '<br/>' : '<br/>'; ?>
                                <?php endif;?>
                            <?php endforeach; ?>
                        </td>
                        <td><?php echo $packet['warehouseName']; ?></td>
                        <td>
                            <?php if ($status == 2) {
                                echo Yii::$app->user->can('确认出库单') ? Html::a('确认', 'confirm?date=' . $date . '&author=' . $author, ['class' => 'btn btn-success']) . ' ' : '';
                                echo Yii::$app->user->can('编辑出库单') ? Html::a('修改', 'update?date=' . $date . '&author=' . $author, ['class' => 'btn btn-success']) : '';
                            } else if ($status == 4) {
                                echo Html::button('已确认', ['class' => 'btn']);
                            } else if ($status == 3) {
                                echo Html::button('已领料', ['class' => 'btn']);
                            } ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

            <?php endforeach; ?>

        <?php endforeach; ?>
        <?php echo count($packetArr) === 0 ? '<tr><td colspan="4"><div class="empty">没有找到数据。</div></td></tr>' : '';?>
        </tbody>
    </table>
    <?php
    echo \yii\widgets\LinkPager::widget([
        'pagination' => $pages,
    ]);
    ?>
</div>
