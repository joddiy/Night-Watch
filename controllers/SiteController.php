<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    const KEY = "SiteController_";
    const EXPIRE = 3;

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->redirect('/site/gpu');
    }


    public function actionGpu()
    {
        $params = Yii::$app->request->get();
        $current_cluster = [];
        if (empty($params['cluster'])) {
            $params['cluster'] = "ncra";
        }
        // cluster and gpu amount
        $redis_key = self::KEY . md5("get_gpu_panel_" . $params['cluster']);
        $cache = Yii::$app->redis->get($redis_key);
        if (empty($cache)) {
            # current power rate
            # current memory rate
            $current_cluster['name'] = $params['cluster'];
            $gpus = [];
            $sql = <<<EOF
select c.gpu_order, utilization as power_rate, round(memory_used / memory_total * 100, 2) as memory_rate, d.add_time
from gpu_list c left join gpu_log d on c.gpu_id = d.gpu_id
where log_id in (select max(b.log_id) as log_id
                 from gpu_list a
                        left join gpu_log b on a.gpu_id = b.gpu_id
                 where a.cluster = :cluster
                 group by b.gpu_id)
EOF;
            $ret = \Yii::$app->getDb()->createCommand($sql, [
                ':cluster' => $params['cluster']
            ])->query();
            foreach ($ret as $item) {
                $gpu_order = $item['gpu_order'];
                if (empty($gpus[$gpu_order])) {
                    $gpus[$gpu_order] = [];
                }
                $gpus[$gpu_order]['power_rate'] = $item['power_rate'];
                $gpus[$gpu_order]['memory_rate'] = $item['memory_rate'];
                $gpus[$gpu_order]['add_time'] = $item['add_time'];
            }
            # current ps
            $sql = <<<EOF
select gpu_order, count(1) as ps_amount
from gpu_list c
       left join gpu_log a on c.gpu_id = a.gpu_id
       inner join gpu_ps b on a.log_id = b.log_id
where a.log_id in (select max(b.log_id) as log_id
                   from gpu_list a
                          left join gpu_log b on a.gpu_id = b.gpu_id
                   where a.cluster = :cluster
                   group by b.gpu_id)
group by a.gpu_id;
EOF;
            $ret = \Yii::$app->getDb()->createCommand($sql, [
                ':cluster' => $params['cluster']
            ])->query();
            foreach ($ret as $item) {
                $gpu_order = $item['gpu_order'];
                if (empty($gpus[$gpu_order])) {
                    $gpus[$gpu_order] = [];
                }
                $gpus[$gpu_order]['ps_amount'] = $item['ps_amount'];
            }
            $current_cluster['gpus'] = $gpus;
            # Users
            $users = [];
            $sql = <<<EOF
select gpu_order,
       b.username,
       GROUP_CONCAT(b.cmdline SEPARATOR '\n') as cmdline,
       count(1)                                                  as ps_amount,
       round(sum(gpu_memory_usage) / max(memory_total) * 100, 2) as user_rate
from gpu_list c
       left join gpu_log a on c.gpu_id = a.gpu_id
       inner join gpu_ps b on a.log_id = b.log_id
where a.log_id in (select max(b.log_id) as log_id
                   from gpu_list a
                          left join gpu_log b on a.gpu_id = b.gpu_id
                   where a.cluster = :cluster
                   group by b.gpu_id)
group by a.gpu_id, b.username
order by gpu_order, user_rate DESC
EOF;
            $ret = \Yii::$app->getDb()->createCommand($sql, [
                ':cluster' => $params['cluster']
            ])->query();
            foreach ($ret as $item) {
                $gpu_order = $item['gpu_order'];
                if (empty($users[$gpu_order])) {
                    $users[$gpu_order] = [];
                }
                $tmp_item = [];
                $tmp_item['username'] = $item['username'];
                $tmp_item['ps_amount'] = $item['ps_amount'];
                $tmp_item['cmdline'] = $item['cmdline'];
                $tmp_item['user_rate'] = $item['user_rate'];
                $users[$gpu_order][] = $tmp_item;
            }
            $current_cluster['users'] = $users;
            Yii::$app->redis->set($redis_key, json_encode($current_cluster));
            Yii::$app->redis->expire($redis_key, self::EXPIRE);
        } else {
            $current_cluster = json_decode($cache, true);
        }
        // cluster and gpu amount
        $redis_key = self::KEY . md5("get_gpu_panel_all");
        $cache = Yii::$app->redis->get($redis_key);
        if (empty($cache)) {
            $sql = <<<EOF
select a.cluster as name, count(1) as amount, round(sum(c.memory_used) / sum(c.memory_total) * 100, 2) as use_r
from gpu_list a
       left join (select gpu_id, max(log_id) as log_id from gpu_log group by gpu_id) as b on a.gpu_id = b.gpu_id
       left join gpu_log c on b.log_id = c.log_id
group by a.cluster
EOF;
            $clusters = \Yii::$app->getDb()->createCommand($sql)->queryAll();
            Yii::$app->redis->set($redis_key, json_encode($clusters));
            Yii::$app->redis->expire($redis_key, self::EXPIRE);
        } else {
            $clusters = json_decode($cache, true);
        }
        return $this->render('gpu', [
            'clusters' => $clusters,
            'current_cluster' => $current_cluster
        ]);
    }

    public function actionCpu()
    {
        return $this->render('working');
    }

    public function actionDisk()
    {
        return $this->render('working');
    }
}
