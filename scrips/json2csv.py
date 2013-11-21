import simplejson
import csv

def main():
    with open('pedestrians.json','r') as f:
        json = f.read()
    data = simplejson.loads(json)

    ofile  = open('pedestrians.csv', "wb")
    writer = csv.writer(ofile, delimiter=',', quotechar='"', quoting=csv.QUOTE_ALL)

    writer.writerow(['ItemID','Address','Date','Time','lat','lng'])

    for row in data:
        writer.writerow(row)

    ofile.close()

main()
