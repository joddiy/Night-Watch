import gpustat
from six.moves import cStringIO as StringIO
import json
import requests


gpustats = gpustat.new_query()

fp = StringIO()

gpustats.print_json(fp=fp)

res = json.loads(fp.getvalue())

r = requests.post("http://ncrs.d2.comp.nus.edu.sg/api/watch-gpu", data=res)

print(r.text)
