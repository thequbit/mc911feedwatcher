import json
import urllib2
import pygal

# construct our API URL with type parameter set to 'alltimesum'
apiurl = 'http://mcsafetyfeed.org/api/counts.php?type=barkingdogs'

# retrieve the JSON object via the API URL
data = json.load( urllib2.urlopen( apiurl ) )

# the count array will hold a list of daily counts, the label array will hold our x-axis labels
counts = []
labels = []

# using PyGal, create a line chart
line_chart = pygal.Line()

line_chart.config.height=800
line_chart.config.width=1000
line_chart.config.show_legend = False

# iterate through the returned JSON array and pull out the counts and the incident type labels
for d in data:
    counts.append( int( d["count"] ) )
    labels.append( d["date"].encode("utf8") ) # PyGal no like uni-code ...

# set the title of our chart to something intelligent
line_chart.title = 'Barking Dog Complaints Per Day'

# set our x-axis labels, as well as the values to be shown on the graph
line_chart.x_labels = labels
line_chart.add('Barking Dog Complaints', counts)

# make sure you tip your local wizard after calling this function ....
output = line_chart.render()

print output
