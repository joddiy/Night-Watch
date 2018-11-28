<?php
/**
 * author:       joddiyzhang <joddiyzhang@gmail.com>
 * createTime:   2018/11/28 2:25 PM
 * fileName :    navbar.php
 */

?>

<nav id="mainnav-container" style="padding-top: 0">
    <div id="mainnav">

        <!--Shortcut buttons-->
        <!--================================-->
        <div id="mainnav-shortcut">
            <ul class="list-unstyled">
                <!--                <li class="col-xs-4" data-content="Additional Sidebar">-->
                <!--                    <a id="demo-toggle-aside" class="shortcut-grid" href="#">-->
                <!--                        <i class="fa fa-magic"></i>-->
                <!--                    </a>-->
                <!--                </li>-->
                <!--                <li class="col-xs-4" data-content="Notification">-->
                <!--                    <a id="demo-alert" class="shortcut-grid" href="#">-->
                <!--                        <i class="fa fa-bullhorn"></i>-->
                <!--                    </a>-->
                <!--                </li>-->
                <!--                <li class="col-xs-4" data-content="Page Alerts">-->
                <!--                    <a id="demo-page-alert" class="shortcut-grid" href="#">-->
                <!--                        <i class="fa fa-bell"></i>-->
                <!--                    </a>-->
                <!--                </li>-->
            </ul>
        </div>
        <!--================================-->
        <!--End shortcut buttons-->

        <!--Menu-->
        <!--================================-->
        <div id="mainnav-menu-wrap">

            <div class="nano">
                <div class="nano-content">
                    <ul id="mainnav-menu" class="list-group">

                        <!--Category name-->
                        <li class="list-header">Navigation</li>

                        <!--Menu list item-->
                        <!--                <li class="active-link">-->
                        <!--                    <a href="index.html">-->
                        <!--                        <i class="fa fa-dashboard"></i>-->
                        <!--                        <span class="menu-title">-->
                        <!--                            <strong>Dashboard</strong>-->
                        <!--                        </span>-->
                        <!--                    </a>-->
                        <!--                </li>-->

                        <li>
                            <a href="/site/gpu">
                                <i class="fa fa-tv"></i>
                                <span class="menu-title">
                                <strong>GPU Monitor</strong>
                        </span>
                            </a>
                        </li>

                        <!--Menu list item-->
                        <li>
                            <a href="/site/cpu">
                                <i class="fa fa-server"></i>
                                <span class="menu-title">
                                <strong>CPU Monitor</strong>
                        </span>
                            </a>
                        </li>

                        <li>
                            <a href="/site/disk">
                                <i class="fa fa-hdd-o"></i>
                                <span class="menu-title">
                                <strong>Disk Usage</strong>
                        </span>
                            </a>
                        </li>

                        <li class="list-divider"></li>

                    </ul>


                    <!--Widget-->
                    <!--================================-->
                    <div class="mainnav-widget">

                        <!-- Show the button on collapsed navigation -->
                        <div class="show-small">
                            <a href="#" data-toggle="menu-widget" data-target="#demo-wg-server">
                                <i class="fa fa-desktop"></i>
                            </a>
                        </div>

                        <!-- Hide the content on collapsed navigation -->
                        <div id="demo-wg-server" class="hide-small mainnav-widget-content">
                            <ul class="list-group">
                                <li class="list-header pad-no pad-ver">Server Status</li>
                                <li class="mar-btm">
                                    <span class="label label-primary pull-right">15%</span>
                                    <p>GPU Usage</p>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar progress-bar-primary" style="width: 15%;">
                                            <span class="sr-only">15%</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="mar-btm">
                                    <span class="label label-mint pull-right">30%</span>
                                    <p>CPU Usage</p>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar progress-bar-mint" style="width: 30%;">
                                            <span class="sr-only">75%</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="mar-btm">
                                    <span class="label label-purple pull-right">20%</span>
                                    <p>Home Usage</p>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar progress-bar-purple" style="width: 20%;">
                                            <span class="sr-only">75%</span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!--================================-->
                    <!--End widget-->

                </div>
            </div>
        </div>
        <!--================================-->
        <!--End menu-->

    </div>
</nav>
