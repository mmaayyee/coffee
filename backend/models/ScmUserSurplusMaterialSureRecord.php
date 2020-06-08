<?php

namespace backend\models;

use common\models\WxMember;
use Yii;

/**
 * This is the model class for table "scm_user_surplus_material_sure_record".
 *
 * @property integer $id
 * @property string $author
 * @property integer $material_id
 * @property integer $add_reduce
 * @property integer $material_num
 * @property string $date
 * @property integer $createTime
 * @property integer $is_sure
 * @property integer $sure_time
 *
 * @property ScmMaterial $material
 * @property WxMember $author0
 */
class ScmUserSurplusMaterialSureRecord extends \yii\db\ActiveRecord {
    public $material_name;
    /** 定义审核状态常量 */
    // 待审核状态
    const SURE = 1;
    // 审核通过
    const SURE_YES = 2;
    // 审核不通过
    const SURE_NO = 3;

    /** 定义添加或者减少剩余物料常量 */
    // 添加物料
    const ADD = 1;
    // 减少物料
    const REDUCE = 2;
    public $material_gram;

    /** @var array 定义审核状态数组 */
    public static $sure = [
        self::SURE     => '待审核',
        self::SURE_YES => '审核通过',
        self::SURE_NO  => '审核不通过',
    ];

    /** @var array 添加或者减少剩余物料数组 */
    public static $addReduce = [
        self::ADD    => '添加',
        self::REDUCE => '减少',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'scm_user_surplus_material_sure_record';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['reason', 'material_id', 'material_num'], 'required'],
            [['material_id', 'add_reduce', 'material_num', 'createTime', 'is_sure', 'sure_time'], 'integer'],
            [['author'], 'string', 'max' => 64],
            [['date'], 'string', 'max' => 10],
            [['reason'], 'string', 'max' => 500],
            [['material_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScmMaterial::className(), 'targetAttribute' => ['material_id' => 'id']],
            [['author'], 'exist', 'skipOnError' => true, 'targetClass' => WxMember::className(), 'targetAttribute' => ['author' => 'userid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id'           => '剩余物料修改记录id',
            'author'       => '申请人',
            'material_id'  => '物料id',
            'add_reduce'   => '加还是减 1-加 2-减',
            'material_num' => '物料数量',
            'date'         => '申请日期',
            'createTime'   => '申请时间',
            'is_sure'      => '审核状态 1-未审核 2-审核通过 3-审核未通过',
            'sure_time'    => '审核时间',
            'reason'       => '修改原因',
            'material_gram' => '散料重量',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial() {
        return $this->hasOne(ScmMaterial::className(), ['id' => 'material_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(WxMember::className(), ['userid' => 'author']);
    }

    /**
     * 批量添加数据
     * @author  zgw
     * @version 2016-10-20
     * @param   array     $data 要添加的数据
     */
    public function addAll($data) {
        return Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['material_id', 'add_reduce', 'material_num', 'reason', 'author', 'date', 'createTime'], $data)->execute();
    }
}
