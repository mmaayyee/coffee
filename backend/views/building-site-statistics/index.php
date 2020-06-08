<?php
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = '楼宇点位统计';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="building-site-statistics-index">
    <?php echo $this->render('_search', ['model' => $searchModel, 'searchData' => $searchData, 'params' => $params]); ?>
    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        [
            'class'  => 'yii\grid\SerialColumn',
            'header' => '序号',
        ],
        [
            'attribute' => '点位编号',
            'value'     => function ($model) {
                return $model->build_number;
            },
        ],
        [
            'attribute' => '点位名称',
            'value'     => function ($model) {
                return $model->name;
            },
        ],
        [
            'attribute' => '运营模式',
            'value'     => function ($model) {
                return $model->organization_type;
            },
        ],
        [
            'attribute' => '所属公司',
            'value'     => function ($model) {
                return $model->org_name;
            },
        ],
        [
            'attribute' => '所属城市',
            'value'     => function ($model) {
                return $model->org_city;
            },
        ],
        [
            'attribute' => '运营开始时间',
            'value'     => function ($model) {
                return $model->create_time ?? '';
            },
        ],

        [
            'attribute' => '运营结束时间',
            'value'     => function ($model) {
                return $model->un_bind_time ?? '';
            },
        ],
        [
            'attribute' => '现编号设备',
            'value'     => function ($model) {
                return $model->equipment_code;
            },
        ],
        [
            'attribute' => '现设备类型',
            'value'     => function ($model) {
                return $model->equipment_type_name;
            },
        ],
        [
            'attribute' => '点位统计',
            'format'    => 'raw',
            'value'     => function ($model) {

                $str  = '<table class="table table-bordered"><thead><tr>';
                $bstr = '<tbody><tr>';
                $i    = 0;
                $max  = count($model->build_site_statistics);
                foreach ($model->build_site_statistics as $date => $statistics) {
                    $style      = '';
                    $buildSite  = explode('|', $statistics);
                    $statistics = $buildSite[0];
                    $isChange   = $buildSite[1];
                    if ($isChange) {
                        $style .= 'background-color:red';
                    }
                    $i += 1;
                    $str .= "<th>$date</th>";
                    $bstr .= "<td style='" . $style . "'>$statistics</td>";
                    if ($i == $max) {
                        $str .= "</tr></thead>";
                        $bstr .= "</tr></tbody>";
                    }
                }
                $bstr .= '</table>';
                return $str . $bstr;
            },
        ],

    ],
]);?>

</div>
