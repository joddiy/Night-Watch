# Installation

## python

At the guest machine:
```
sudo apt install python-pip
sudo pip install gpustat
sudo pip install requests
```

## crontab

At the guest machine:
```
* * * * * /usr/bin/python /home/worker/Night-Watch/py/watch_gpu.py
```
This script will upload its gpu info to the host.

At the host machine:
```
0 4 1 * * /usr/bin/php5.6 /home/worker/Night-Watch/yii hello/renew
```
This script will archive gpu logs.

### mysql table

gpu_list
```
create table night_watch.gpu_list
(
  gpu_id    int auto_increment
    primary key,
  cluster   varchar(20)                         not null,
  gpu_order int                                 not null,
  add_time  timestamp default CURRENT_TIMESTAMP not null
);
```
This table records your gpu info. Please be cafeful, your guest machine's host name should be identical with the cluster field here.

gpu_log
```
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
```
This table records each gpu's logs.

gpu_ps
```
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
)
  comment 'gpu process';

create index gpu_ps_add_time_index
  on night_watch.gpu_ps (add_time);

create index gpu_ps_log_id_index
  on night_watch.gpu_ps (log_id);
```
This table records each user's logs.
