sudo apt-get install python-pip
/usr/bin/pip2 install gpustat
/usr/bin/pip2 install psutil
/usr/bin/pip2 install requests
/usr/bin/python PathTo/Night-Watch/py/watch_gpu.py


# add to crontab
# * * * * * /usr/bin/python PathTo/Night-Watch/py/watch_gpu.py