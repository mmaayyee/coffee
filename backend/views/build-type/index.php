<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\BuildTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '楼宇类型管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="build-type-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?php echo $this->render('_search', ['buildTypeName' => $buildTypeName,'buildTypeCode' => $buildTypeCode]); ?>
    <p>
        <?=Html::a('添加楼宇类型', ['create'], ['class' => 'btn btn-success'])?>
    </p>
    <div id="w1" class="grid-view">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>类型名称</th>
                    <th>楼宇类型编码</th>
                    <th>创建时间</th>
                    <th class="action-column">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($buildTypeList as $key => $buildType): ?>
                <tr data-key="<?php echo $key + 1; ?>">
                    <td><?php echo $key + 1; ?></td>
                    <td><?php echo $buildType['type_name'] ?></td>
                    <td><?php echo $buildType['type_code'] ?></td>
                    <td><?php echo $buildType['create_date'] ?></td>
                    <td>
                        <a href="/index.php/build-type/update?id=<?php echo $buildType['id'] ?>" title="更新" aria-label="更新">
                            <span class="glyphicon glyphicon-pencil"></span>
                        </a>
                    </td>
                </tr>
            <?php endforeach?>
            </tbody>
        </table>
    </div>
</div>
