<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gpu_list".
 *
 * @property int $gpu_id
 * @property string $cluster
 * @property int $gpu_order
 * @property string $add_time
 */
class GpuList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gpu_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cluster', 'gpu_order'], 'required'],
            [['gpu_order'], 'integer'],
            [['add_time'], 'safe'],
            [['cluster'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'gpu_id' => 'Gpu ID',
            'cluster' => 'Cluster',
            'gpu_order' => 'Gpu Order',
            'add_time' => 'Add Time',
        ];
    }
}
