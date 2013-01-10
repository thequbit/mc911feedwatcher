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

Example API Call:

	http://monroe911.mycodespace.net/getapi.php?startdate=2013-1-1
	
Example Result:

	{
		"apiversion": "1.0",
		"errorcode": "0",
		"errortext": "No errors reported.",
		"querytime": "0.0031869411468506",
		"resultcount": "244",
		"results":
			[
				{
					"event":"Parking complaint",
					"address":"1200 BROOKS AV,Rochester",
					"pubdate":"2013-01-09",
					"pubtime":"23:37:00",
					"status":"ONSCENE",
					"incidentid":"MCOP130093564",
					"scrapedatetime":"2013-01-09 23:45:00"
				},

				{
					"event":"Parking complaint",
					"address":"67 BROOKFIELD RD, Rochester",
					"pubdate":"2013-01-09",
					"pubtime":"23:09:00",
					"status":"WAITING",
					"incidentid":"CTYP130093511",
					"scrapedatetime":"2013-01-09 23:45:00"
				}
			]
	}