mc911feedwatcher
================

Pulls the XML of Monroe County's 911 feed and provides a web API 

Scraper runs every 60 seconds and pushes only unique Incident ID's and Incident Status's to the DB.

Next to come is a nice little web API that will host up any data you want.

Items saved:

	incidentid - int
	event - varchar(255)
	address - varchar(255)
	pubdate - date
	pubtime - time
	status - varchar(255)
	itemid - varchar(255)
	scrapedatetime - datetime


