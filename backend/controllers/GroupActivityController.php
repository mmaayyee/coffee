<?php

namespace backend\controllers;

use backend\models\GroupActivity;
use backend\models\GroupActivitySearch;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * GroupActivityController implements the CRUD actions for GroupActivity model.
 */
class GroupActivityController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 设置
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSet()
    {
        if (!Yii::$app->user->can('拼团活动设置展示')) {
            return $this->redirect(['site/login']);
        }
        $groupDate                                               = GroupActivity::getSetting();
        $groupDate                                               = Json::decode($groupDate, true);
        $groupDate['config_value']                               = Json::decode($groupDate['config_value'], true);
        $groupDate['config_value']['participation_organization'] = Json::decode($groupDate['config_value']['participation_organization'], true);
        $groupDate['config_value']['goods_type']                 = Json::decode($groupDate['config_value']['goods_type'], true);
        // print_r($groupDate);exit;
        return $this->render('set', [
            'config_value' => $groupDate['config_value'],
            'Organization' => $groupDate['Organization'],
        ]);

    }
    /**
     * 添加/修改
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSave()
    {
        if (!Yii::$app->user->can('拼团活动添加/编辑')) {
            return $this->redirect(['site/login']);
        }
        $groupId = Yii::$app->request->get('group_id', ''); // 拼团表id
        $data    = GroupActivity::getDetails($groupId);
        return $this->render('save', [
            'taskOptionsList' => $data,
        ]);
    }
    /**
     * 上线排序
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSort()
    {
        if (!Yii::$app->user->can('拼团活动线上排序')) {
            return $this->redirect(['site/login']);
        }
        $data = GroupActivity::getSort();
        return $this->render('sort', [
            'group' => $data,
        ]);
    }
    /**
     * 统计
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionStatistics()
    {
        if (!Yii::$app->user->can('拼团活动统计')) {
            return $this->redirect(['site/login']);
        }
        return $this->render('statistics', [
            'data' => GroupActivity::getStatistics(),
        ]);
    }

    /**
     * Lists all GroupActivity models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('拼团活动列表展示')) {
            return $this->redirect(['site/login']);
        }
        $searchModel  = new GroupActivitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

//         var_dump($dataProvider);exit;

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GroupActivity model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('拼团活动列表展示')) {
            return $this->redirect(['site/login']);
        }
        $data = Json::decode(GroupActivity::getDetails($id));
        $obj  = $data['data']['details'];
        if (isset($data['data']['details']['price_ladder'])) {
            $str = '';
            foreach ($obj['price_ladder'] as $key => $value) {
                $str .= '参团人数' . $value['tuxedo_num'] . '=>拼团价格' . $value['group_price'] . ';';
            }
            $data['data']['details']['price_ladder'] = $str;
        }
        foreach ($data['data']['coffee_product'] as $key => $value) {
            $arr[$value['cf_product_id']] = $value['cf_product_name'];
        }

        if (isset($data['data']['details']['drink_ladder'])) {
            $str = '';
            foreach ($obj['drink_ladder'] as $key => $value) {
                $name = isset($arr[$value['cf_product_id']]) ? $arr[$value['cf_product_id']] : $value['cf_product_id'];
                $str .= '饮品名称:' . $name . '=>饮品数量:' . $value['group_attached_num'] . ';';
            }
            $data['data']['details']['drink_ladder'] = $str;
        }

        $obj = (object) $data['data']['details'];

        return $this->render('view', [
            'model' => $obj,
        ]);
    }

    /**
     * [拼团数据统计-排名导出Excel]
     * get
     * @author  du
     * @version 2018-07-11
     * @return  [json]
     */
    public function actionGetRanking()
    {
        $date = date("Y-m-d", strtotime("-1 day"));
        $date = Yii::$app->request->get('date', $date); // 时间
        $type = Yii::$app->request->get('type', ''); // 活动类别
        $list = GroupActivity::getRanking(['date' => $date, 'type' => $type]);
        $arr  = [];
        foreach ($list['data'] as $key => $value) {
            $arr[$key]['main_title']        = $value['main_title']; // 活动名称
            $arr[$key]['heat_sort']         = $value['heat_sort'] + 1; // 热度排名
            $arr[$key]['heat']              = $value['heat']; // 热度数量
            $arr[$key]['frequency_sort']    = $value['frequency_sort'] + 1; // 成团率排名
            $arr[$key]['frequency']         = $value['frequency']; // 成团率数量
            $arr[$key]['pull_the_new_sort'] = $value['pull_the_new_sort'] + 1; // 拉新用户排名
            $arr[$key]['pull_the_new']      = $value['pull_the_new']; // 拉新用户数量
        }
        $name = ['活动名称', '热度排名', '热度数量', '成团率排名', '成团率数量', '拉新用户排名', '拉新用户数量'];
        GroupActivity::exportExcel($name, $arr, '拼团-排名数据');
    }
    /**
     * [拼团数据统计-单团详细数据导出Excel]
     * get
     * @author  du
     * @version 2018-07-12
     * @return  [json]
     */
    public function actionGetSingle()
    {
        $groupId   = Yii::$app->request->get('group_id', ''); // 活动id
        $beginTime = Yii::$app->request->get('begin_time', ''); // 开始时间
        $endTime   = Yii::$app->request->get('end_time', ''); // 结束时间
        $type      = Yii::$app->request->get('type', ''); // 活动类别
        $list      = GroupActivity::getSingle([
            'group_id'   => $groupId,
            'begin_time' => $beginTime,
            'end_time'   => $endTime,
            'type'       => $type,
        ]);
        $name = ['活动名称', '发起用户数', '发起总团数', '成功总团数', '拉新用户数', '销量(杯数)', '销售额', '时间'];
        GroupActivity::exportExcel($name, $list['data'], '拼团-单团详细数据');
    }

}
