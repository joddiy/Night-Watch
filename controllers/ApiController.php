<?php
/**
 * author:       joddiyzhang <joddiyzhang@gmail.com>
 * createTime:   11/03/2018 9:23 PM
 * fileName :    ApiController.php
 */

namespace app\controllers;

use app\components\Common;
use app\components\RestController;
use app\components\XMMitalk;
use app\components\XMPassport;
use app\models\AdminLog;
use app\models\AdminToken;
use app\models\AdminUser;
use app\components\XMCas;
use app\models\Company;
use app\models\GpuList;
use app\models\GpuLog;
use app\models\GpuPs;
use app\models\Graph;
use app\models\LbArchitecture;
use app\models\LbBem;
use app\models\News;
use app\models\OSISoft;
use Yii;
use yii\base\ErrorException;
use yii\db\Exception;


/**
 * Class ApiController
 * @package app\controllers
 */
class ApiController extends RestController
{
    const KEY = "ApiController";
    const EXPIRE = 3;

    /**
     * @return array
     */
    public function actionWatchGpu()
    {

        try {
            $params = Yii::$app->request->getRawBody();
            $log = json_decode($params, true);
            $hostname = $log['hostname'];
            $current_time = date('Y-m-d H:i:s', strtotime($log['query_time']));
            $transaction = \Yii::$app->getDb()->beginTransaction();
            try {
                foreach ($log['gpus'] as $item) {
                    $gpu_order = $item['index'];
                    $gpu = GpuList::findOne(["cluster" => $hostname, "gpu_order" => $gpu_order]);
                    $new_gpu_log = new GpuLog();
                    $new_gpu_log->gpu_id = $gpu->gpu_id;
                    $new_gpu_log->temperature = $item['temperature.gpu'];
                    $new_gpu_log->utilization = $item['utilization.gpu'];
                    $new_gpu_log->power_draw = $item['power.draw'];
                    $new_gpu_log->power_max = $item['enforced.power.limit'];
                    $new_gpu_log->memory_used = $item['memory.used'];
                    $new_gpu_log->memory_total = $item['memory.total'];
                    $new_gpu_log->add_time = $current_time;
                    $new_gpu_log->insert();
                    foreach ($item['processes'] as $ps) {
                        $new_ps = new GpuPs();
                        $new_ps->log_id = $new_gpu_log->log_id;
                        $new_ps->username = $ps['username'];
                        $new_ps->command = $ps['command'];
                        $new_ps->cmdline = $ps['cmdline'];
                        $new_ps->gpu_memory_usage = $ps['gpu_memory_usage'];
                        $new_ps->pid = $ps['pid'];
                        $new_ps->add_time = $current_time;
                        $new_ps->insert();
                    }
                }
            } catch (\Throwable $e) {
                $transaction->rollBack();
                return $this->formatRestResult(self::FAILURE, $e->getMessage());
            }
            $transaction->commit();

            return $this->formatRestResult(self::SUCCESS, []);
        } catch (\Exception $e) {
            return $this->formatRestResult(self::FAILURE, $e->getMessage());
        }
    }

    public function actionGetServerStatus()
    {
        try {
            // cluster and gpu amount
            $redis_key = self::KEY . md5("get_gpu_status");
            $cache = Yii::$app->redis->get($redis_key);
            if (empty($cache)) {
                # gpu status
                $data = [];
                $sql = <<<EOF
select round(sum(memory_used) / sum(memory_total) * 100, 2) as memory_rate
from gpu_log d
where log_id in (select max(b.log_id) as log_id
                 from gpu_list a
                        left join gpu_log b on a.gpu_id = b.gpu_id
                 group by b.gpu_id)
EOF;
                $ret = \Yii::$app->getDb()->createCommand($sql)->queryOne();
                $data['gpu'] = $ret['memory_rate'];
                Yii::$app->redis->set($redis_key, json_encode($data));
                Yii::$app->redis->expire($redis_key, self::EXPIRE);
            } else {
                $data = json_decode($cache, true);
            }
            return $this->formatRestResult(self::SUCCESS, $data);
        } catch (\Exception $e) {
            return $this->formatRestResult(self::FAILURE, $e->getMessage());
        }
    }

    public function actionGetGpuHistory()
    {
        try {
            $params = Yii::$app->request->get();
            $data = [];
            $redis_key = self::KEY . md5("get_gpu_history_" . $params['cluster']);
            $cache = Yii::$app->redis->get($redis_key);
            if (empty($cache)) {
                $sql = <<<EOF
select a.gpu_order,
       utilization     as power_rate,
       round(memory_used / memory_total * 100, 2) as memory_rate,
       DATE_FORMAT(b.add_time, "%m-%d %H:%i") as add_time
from gpu_list a
       left join gpu_log b on a.gpu_id = b.gpu_id
where a.cluster = :cluster
  and b.add_time >= now() - INTERVAL 6 HOUR
EOF;
                $ret = \Yii::$app->getDb()->createCommand($sql, [
                    ':cluster' => $params['cluster']
                ])->query();
                foreach ($ret as $item) {
                    $gpu_order = $item['gpu_order'];
                    if (empty($data[$gpu_order])) {
                        $data[$gpu_order] = [];
                    }
                    $tmp_item = [];
                    $tmp_item['power_rate'] = $item['power_rate'];
                    $tmp_item['memory_rate'] = $item['memory_rate'];
                    $tmp_item['add_time'] = $item['add_time'];
                    $data[$gpu_order][] = $tmp_item;
                }
                Yii::$app->redis->set($redis_key, json_encode($data));
                Yii::$app->redis->expire($redis_key, self::EXPIRE);
            } else {
                $data = json_decode($cache, true);
            }
            return $this->formatRestResult(self::SUCCESS, $data);
        } catch (\Exception $e) {
            return $this->formatRestResult(self::FAILURE, $e->getMessage());
        }
    }
}