<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gpu_ps".
 *
 * @property int $id
 * @property int $log_id
 * @property string $username
 * @property string $command
 * @property string $cmdline
 * @property int $gpu_memory_usage
 * @property int $pid
 * @property string $add_time
 */
class GpuPs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gpu_ps';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['log_id', 'gpu_memory_usage', 'pid'], 'required'],
            [['log_id', 'gpu_memory_usage', 'pid'], 'integer'],
            [['command', 'cmdline'], 'string'],
            [['add_time'], 'safe'],
            [['username'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'log_id' => 'Log ID',
            'username' => 'Username',
            'command' => 'Command',
            'cmdline' => 'Cmdline',
            'gpu_memory_usage' => 'Gpu Memory Usage',
            'pid' => 'Pid',
            'add_time' => 'Add Time',
        ];
    }
}
