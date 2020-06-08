<?php

use yii\helpers\Html;
use yii\grid\GridView;

use backend\assets\AppAsset;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\EquipProcessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '设备工序管理';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile("/css/jquery.minicolors.css", [AppAsset::className(), 'depends' => 'backend\assets\AppAsset']);
$this->registerJsFile("/js/jquery.minicolors.min.js", ["depends" => [\yii\web\JqueryAsset::className()]]);

$this->registerJs('
//调用取色器
    $(".color").each( function() {
        $(this).minicolors({
            control: $(this).attr("data-control") || "hue",
            defaultValue: $(this).attr("data-defaultValue") || " ",
            inline: $(this).attr("data-inline") === "true",
            letterCase: $(this).attr("data-letterCase") || "lowercase",
            opacity: $(this).attr("data-opacity"),
            position: $(this).attr("data-position") || "bottom left",
            change: function(hex, opacity) {
                if( !hex ) return;
                if( opacity ) hex += ", " + opacity;
                try {
                } catch(e) {}
            },
            theme: "bootstrap"
        });
    });
');

?>
<div class="equip-process-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加设备工序', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => '工序名称',
                'value'     => function ($model) {
                    return $model->process_name;
                },
            ],
            [
                'label' => '工序英文名称',
                'value'     => function ($model) {
                    return $model->process_english_name;
                },
            ],
            [
                'label' => '色块',
                'format'    => 'raw',
                'value'     => function ($model) {
                    return '<input class="form-control color"  data-control="hue" value="'.$model->process_color.'" disabled="disabled" />';
                },
            ],
            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => ' {update}{delete}',
                'buttons'  => [
                    // 下面代码来自于 yii\grid\ActionColumn 简单修改了下
                    'delete' => function ($url, $model, $key) {
                        $options = [
                                'onclick' =>'if(confirm("确定删除吗？")){$.get(\'/equip-process/delete-verify?id='.$model->id.'&processName='.$model->process_name.'\','
                                . 'function(data){'
                                . 'if(data == 1){location.reload()}'
                                . 'else{alert(\'删除失败，请检该工序是否在被使用\')}})'
                                . '};'
                                . 'return false;'
                            ];
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', '/equip-process/delete-verify?id='.$model->id.'&processName='.$model->process_name,$options);
                    },
                ],
            ],
        ],
    ]); ?>

</div>
