#!/usr/bin/python
from glob import glob
from weasyprint import HTML
from pikepdf import Pdf

for file in glob('/tmp/*.html'):
    HTML(file).write_pdf(file[:-4]+"pdf")

res = Pdf.new()

for file in glob('/tmp/*.pdf'):
    src = Pdf.open(file)
    res.pages.extend(src.pages)

res.save('/tmp/res.pdf')