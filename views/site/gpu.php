<?php
/**
 * author:       joddiyzhang <joddiyzhang@gmail.com>
 * createTime:   2018/11/28 2:49 PM
 * fileName :    gpu.php
 */


/* @var $clusters */

$this->title = 'Gpu Monitor';

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="site-index">

    <!--Display all clusters-->
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <div class="row">

        <?php
        foreach ($clusters as $cluster) {
            ?>
            <div class="col-sm-6 col-lg-3">

                <!--Registered User-->
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <div class="panel media pad-all active">
                    <div class="media-left">
                        <span class="icon-wrap icon-wrap-sm icon-circle bg-success">
                            <i class="fa fa-server fa-2x"></i>
                        </span>
                    </div>

                    <div class="media-body">
                        <p class="text-2x mar-no text-thin"><?= $cluster['name'] ?></p>
                        <p class="text-muted mar-no">GPU Amount: <?= $cluster['amount'] ?></p>
                    </div>
                </div>
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

            </div>
            <?php
        }
        ?>
    </div>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
</div>