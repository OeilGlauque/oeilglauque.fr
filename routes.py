import re
import os

def rep(m):
    return m.group(1)+"#["+m.group(2)+": "+m.group(3)+"]"

regex = re.compile("([ ]*)\/\*\*\n[ ]*\* @(Route.*, name)=(.*)\n[ ]*\*\/")

path = "./src/Controller"
for file in os.listdir(path):
    if file.endswith(".php"):
        with open(os.path.join(path,file)) as f:
            a = f.read()
        b = regex.sub(rep,a)
        with open(os.path.join(path,file),"w") as f:
            f.write(b)
