import _mysql as mysql
import json

def getblank():

    #print "Getting Blank ..."
 
    host='lisa.duffnet.local'
    user='mc911'
    passwd='password123%%%'
    dbname='mc911feed'

    database = mysql.connect(host=host,user=user,passwd=passwd,db=dbname)
    query = "select eventtype from eventtypes"
    database.query(query)
    dbresult=database.store_result()
    blank = {}
    for row in dbresult.fetch_row(maxrows=0):
        event = row[0].lower()
        count = 0
        if not event == '':
            blank[event] = count
    return blank

def monthavg(year,month):

    host='lisa.duffnet.local'
    user='mc911'
    passwd='password123%%%'
    dbname='mc911feed'

    print "Getting month average ..."

    database = mysql.connect(host=host,user=user,passwd=passwd,db=dbname)
    query = """select event, count(distinct itemid) as count from incidents 
             where year(pubdate) = {0} and month(pubdate) = {1} 
             group by event""".format(year,month)
    database.query(query)
    dbresult=database.store_result()
    avgs = getblank()
    for row in dbresult.fetch_row(maxrows=0):
        event = row[0].lower()
        count = row[1]
        avgs[event] = count
    return avgs

def main():

    print "Start."

    months = []
    for i in range(1,13):
        months.append(monthavg(2013,i))
    #print len(months)

    es = getblank()
    events = []
    for e in es.items():
        evt = e[0]
        #print "Event: ".format(evt)
        #event = {}
        #event['event'] = e['event']
        avgs = []
        for i in range(0,12):
            if evt in months[i]:
                avg = months[i][evt]
            else:
                avg = 0
            avgs.append(avg)
            
        event = {'event': evt, 'avgs': avgs }
        events.append(event)

    j = json.dumps(events,indent=4,separators=(',',': '))

    print "Writing out file ..."

    with open("avgs.json",'w') as f:
        f.write(j)

    print "Stop."

main()
