<?php

namespace frontend\models;

use backend\models\DistributionTask;
use frontend\models\FrontendDistributionTask;
use Yii;

/**
 * This is the model class for table "distribution_task_imgurl".
 *
 * @property int $id ID
 * @property string $task_id 运维任务ID
 * @property string $imgurl 运维

任务图片路径
 *
 * @property DistributionTask $task
 */
class DistributionTaskImgurl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'distribution_task_imgurl';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'imgurl'], 'required'],
            [['task_id'], 'integer'],
            [['imgurl'], 'string', 'max' => 255],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => DistributionTask::className(), 'targetAttribute' => ['task_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'      => 'ID',
            'task_id' => 'Task ID',
            'imgurl'  => 'Imgurl',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(DistributionTask::className(), ['id' => 'task_id']);
    }
    /**
     * 获取任务图片url
     * @author wangxiwen
     * @version 2018-07-07
     * @param int $taskId 任务ID
     * @return array
     */
    public static function getTaskImgUrlList($taskId)
    {
        return self::find()
            ->where(['task_id' => $taskId])
            ->select('imgurl')
            ->column();
    }
    /**
     * 更新运维任务图片
     * @author wangxiwen
     * @version-07-07
     * @param int $taskId 任务ID
     * @param string $imgFilePath 图片路径
     * @return boolean
     */
    public static function saveTaskImgurl($taskId, $imgFilePath)
    {
        $taskImg          = new self();
        $taskImg->task_id = $taskId;
        $taskImg->imgurl  = $imgFilePath;
        $taskImg->save();
    }

    /**
     * 上传任务清洗图片
     * @author zhenggangwei
     * @date   2019-09-03
     * @param  array       $params 提交的数据
     * @param  integer     $taskId 任务ID
     * @return boolen
     */
    public static function uplaodTaskImage($params, $taskId)
    {
        if (empty($params['taskimg'])) {
            return true;
        }
        $savePath = 'uploads/' . date("Y-m-d");
        if (!file_exists(Yii::$app->basePath . '/web/' . $savePath)) {
            mkdir(Yii::$app->basePath . '/web/' . $savePath, 0777, true);
        }
        $saveData = [];
        foreach ($params['taskimg'] as $key => $img) {
            if (!$img) {
                continue;
            }
            $fileName  = "/" . $key . time() . $taskId . '.jpg';
            $uploadImg = FrontendDistributionTask::getUploadImg($img);
            file_put_contents(Yii::$app->basePath . '/web/' . $savePath . $fileName, $uploadImg);
            $saveData[] = [
                $taskId,
                $savePath . $fileName,
            ];
        }
        if (!$saveData) {
            return true;
        }
        return Yii::$app->db->createCommand()->batchInsert(self::tablename(), ['task_id', 'imgurl'], $saveData)->execute();
    }

}
