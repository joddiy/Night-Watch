<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gpu_log".
 *
 * @property int $log_id
 * @property int $gpu_id
 * @property int $temperature
 * @property int $utilization
 * @property int $power_draw
 * @property int $power_max
 * @property int $memory_used
 * @property int $memory_total
 * @property string $add_time
 */
class GpuLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gpu_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gpu_id', 'temperature', 'utilization', 'power_draw', 'power_max', 'memory_used', 'memory_total'], 'required'],
            [['gpu_id', 'temperature', 'utilization', 'power_draw', 'power_max', 'memory_used', 'memory_total'], 'integer'],
            [['add_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'log_id' => 'Log ID',
            'gpu_id' => 'Gpu ID',
            'temperature' => 'Temperature',
            'utilization' => 'Utilization',
            'power_draw' => 'Power Draw',
            'power_max' => 'Power Max',
            'memory_used' => 'Memory Used',
            'memory_total' => 'Memory Total',
            'add_time' => 'Add Time',
        ];
    }
}
