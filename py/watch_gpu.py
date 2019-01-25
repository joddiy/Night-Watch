import gpustat
from six.moves import cStringIO as StringIO
import json
import requests
import psutil

gpustats = gpustat.new_query()

fp = StringIO()

gpustats.print_json(fp=fp)

raw_json = json.loads(fp.getvalue())

for gpu_id, gpu in enumerate(raw_json['gpus']):
    for process_id, process in enumerate(gpu['processes']):
        pid = process['pid']
        raw_json['gpus'][gpu_id]['processes'][process_id]['cmdline'] = " ".join(psutil.Process(pid).cmdline())

json_str = json.dumps(raw_json)

r = requests.post("http://ncrs.d2.comp.nus.edu.sg/api/watch-gpu", data=json_str)

print(r.text)
