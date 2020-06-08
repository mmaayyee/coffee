<?php

namespace backend\models;

use Yii;
use common\models\WxMember;

/**
 * This is the model class for table "scm_user_surplus_material_sure_record_gram".
 *
 * @property integer $id
 * @property string $date
 * @property integer $createTime
 * @property integer $is_sure
 * @property integer $sure_time
 * @property string $reason
 * @property string $author
 * @property integer $add_reduce
 * @property integer $supplier_id
 * @property integer $material_gram
 * @property integer $material_type_id
 */
class ScmUserSurplusMaterialSureRecordGram extends \yii\db\ActiveRecord
{
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

    /** @var array 添加或者减少剩余物料数组 */
    public static $addReduce = [
        self::ADD    => '添加',
        self::REDUCE => '减少',
    ];

    /** @var array 定义审核状态数组 */
    public static $sure = [
        self::SURE     => '待审核',
        self::SURE_YES => '审核通过',
        self::SURE_NO  => '审核不通过',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scm_user_surplus_material_sure_record_gram';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['createTime', 'is_sure', 'sure_time', 'add_reduce', 'supplier_id', 'material_gram', 'material_type_id'], 'integer'],
            [['date'], 'string', 'max' => 10],
            [['reason'], 'string', 'max' => 500],
            [['author'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Date',
            'createTime' => 'Create Time',
            'is_sure' => 'Is Sure',
            'sure_time' => 'Sure Time',
            'reason' => '原因',
            'author' => 'Author',
            'add_reduce' => 'Add Reduce',
            'supplier_id' => 'Supplier ID',
            'material_gram' => 'Material Gram',
            'material_type_id' => 'Material Type ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(WxMember::className(), ['userid' => 'author']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(ScmSupplier::className(), ['id' => 'supplier_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialType()
    {
        return $this->hasOne(ScmMaterialType::className(), ['id' => 'material_type_id']);
    }
}
