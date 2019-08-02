<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\GpuList;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";

        return ExitCode::OK;
    }

    public function actionRenew()
    {
        $today =  date("Y-m-d");
        $sql = <<<EOF
RENAME TABLE gpu_log TO gpu_log_{$today};
EOF;
        \Yii::$app->getDb()->createCommand($sql)->execute();

        $sql = <<<EOF
create table night_watch.gpu_log
(
  log_id       int auto_increment
    primary key,
  gpu_id       int                                 not null,
  temperature  int                                 not null,
  utilization  int                                 not null,
  power_draw   int                                 not null,
  power_max    int                                 not null,
  memory_used  int                                 not null,
  memory_total int                                 not null,
  add_time     timestamp default CURRENT_TIMESTAMP not null
);

create index gpu_log_add_time_index
  on night_watch.gpu_log (add_time);

create index gpu_log_gpu_id_index
  on night_watch.gpu_log (gpu_id);
EOF;
        \Yii::$app->getDb()->createCommand($sql)->execute();

        $sql = <<<EOF
RENAME TABLE gpu_ps TO gpu_ps_{$today};
EOF;
        \Yii::$app->getDb()->createCommand($sql)->execute();

        $sql = <<<EOF
create table night_watch.gpu_ps
(
  id               int auto_increment
    primary key,
  log_id           int                                 not null,
  username         varchar(32)                         null,
  command          text                                null,
  cmdline          text                                null,
  gpu_memory_usage int                                 not null,
  pid              int                                 not null,
  add_time         timestamp default CURRENT_TIMESTAMP not null
);

create index gpu_ps_add_time_index
  on night_watch.gpu_ps (add_time);

create index gpu_ps_log_id_index
  on night_watch.gpu_ps (log_id);
EOF;
        \Yii::$app->getDb()->createCommand($sql)->execute();


    }
}
