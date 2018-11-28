<?php
/**
 * author:       joddiyzhang <joddiyzhang@gmail.com>
 * createTime:   2018/11/28 2:49 PM
 * fileName :    gpu.php
 */

use app\assets\AppAsset;
use Faker\Factory;


/* @var $clusters */
/* @var $current_cluster */

$this->title = 'GPU Monitor';

$this->params['breadcrumbs'][] = $this->title;


AppAsset::addScript($this, '/nifty-v2.2/template/plugins/morris-js/morris.min.js');
AppAsset::addScript($this, '/nifty-v2.2/template/plugins/morris-js/raphael-js/raphael.min.js');
AppAsset::addScript($this, '/js/gpu.js');
AppAsset::register($this);

$faker = Factory::create();
?>

<div class="site-index">

    <!--Display all clusters-->
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <div class="row">

        <?php
        $faker->seed(0);
        foreach ($clusters as $cluster) {
            ?>
            <div class="col-sm-3 col-lg-3">

                <!--Tile with progress bar (Comments)-->
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <div class="panel panel-mint panel-colorful" style="background-color: <?= $faker->safeHexColor ?>">
                    <div class="pad-all media">
                        <div class="media-left">
                        <span class="icon-wrap icon-wrap-xs">
                            <i class="fa fa-server fa-3x"></i>
                        </span>
                        </div>
                        <div class="media-body">
                            <p class="h3 text-thin media-heading"><?= $cluster['use_r'] ?>%</p>
                            <small class="text-uppercase"><?= $cluster['name'] ?></small>
                        </div>
                    </div>

                    <div class="progress progress-xs progress-dark-base mar-no">
                        <div role="progressbar" aria-valuenow="45.9" aria-valuemin="0" aria-valuemax="100"
                             class="progress-bar progress-bar-light" style="width: <?= $cluster['use_r'] ?>%"></div>
                    </div>

                    <div class="pad-all text-right">
                        <small><span class="text-semibold"><i
                                        class="fa fa-tv fa-fw"></i> <?= $cluster['amount'] ?></span> GPUS
                        </small>
                    </div>
                </div>
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <!--End Tile with progress bar (Comments)-->

            </div>
            <?php
        }
        ?>
    </div>

    <?php
    if (!empty($current_cluster)) {
        $current_cluster_name = $current_cluster['name'];
        foreach ($current_cluster['gpus'] as $key => $gpu) {
            ?>
            <h3 class="text-thin mar-btm"><?php echo strtoupper($current_cluster_name . " #" . $key) ?></h3>
            <div class="row">
                <div class="col-lg-6">

                    <!--Network Line Chart-->
                    <!--===================================================-->
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">Overview</h3>
                        </div>

                        <!--Morris line chart placeholder-->
                        <div id=<?php echo "morris-chart-" . $current_cluster_name . $key ?> class="morris-full-content"></div>
                        <!--Chart information-->
                        <div class="panel-body bg-primary" style="position:relative;z-index:2">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-xs-6">

                                            <!--Server load stat-->
                                            <div class="pad-ver media">
                                                <div class="media-left">
                                                <span class="icon-wrap icon-wrap-xs">
                                                    <i class="fa fa-dashboard fa-2x"></i>
                                                </span>
                                                </div>

                                                <div class="media-body">
                                                    <p class="h3 text-thin media-heading"><?= $gpu['memory_rate'] ?>
                                                        %</p>
                                                    <small class="text-uppercase">Current Memory Usage</small>
                                                </div>
                                            </div>

                                            <!--Progress bar-->
                                            <div class="progress progress-xs progress-dark-base mar-no">
                                                <div class="progress-bar progress-bar-light"
                                                     style="width: <?= $gpu['memory_rate'] ?>%"></div>
                                            </div>

                                        </div>
                                        <div class="col-xs-3">
                                            <!-- User Online -->
                                            <div class="text-center">
                                                <span class="text-3x text-thin"><?= $gpu['power_rate'] ?>%</span>
                                                <p>Current Power Usage</p>
                                            </div>
                                        </div>
                                        <div class="col-xs-3">
                                            <!-- User Online -->
                                            <div class="text-center">
                                                <span class="text-3x text-thin"><?= $gpu['ps_amount'] ?></span>
                                                <p>Current Processor Amount</p>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                    <!--===================================================-->
                    <!--End network line chart-->
                </div>

                <div class="col-sm-6">
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">Users</h3>
                        </div>
                        <div class="panel-body">
                            <?php
                            foreach ($current_cluster['users'][$key] as $item) {
                                $faker->seed(20);
                                ?>
                                <!--Small-->
                                <!--===================================================-->
                                <p><?= $item['username'] ?> [process: <?= $item['ps_amount'] ?>]</p>
                                <div class="progress">
                                    <div style="width: <?= $item['user_rate'] ?>%;
                                            background-color: <?= $faker->safeHexColor ?>"
                                         class="progress-bar"><?= $item['user_rate'] ?>%
                                    </div>
                                </div>
                                <!--===================================================-->
                                <?php
                            }
                            ?>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    ?>
</div>
