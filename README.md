mc911feedwatcher
================

Pulls the XML of Monroe County's 911 feed and provides a web API 

Scraper runs every 60 seconds and pushes only unique Incident ID's and Incident Status's to the DB.

Next to come is a nice little web API that will host up any data you want.


GET API
-----------------------------

Description:

	This is the primary API of the system, and allows access to all database contents.  This
	API will return a JSON object that has within it all incidents from the specified start date.

Example API Call:

	http://monroe911.mycodespace.net/getapi.php?startdate=2013-1-1

Variables Passed In:

	startdate - start date to return incidents from, in YYYY-MM-DD format
	
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


EVENT TYPE API
-----------------------------

Description:

	This simply returns a look up table for the event types to their respective ID.  The
	ID is used in other API calls.

Example API Call:

	http://monroe911.mycodespace.net/eventtypeapi.php

Variables Passed In:

	- none -

Example Results:

	{
		"5": "parking complaint",
		"6": "report of something burning inside not involving the structure",
		"7": "hit and run, no injury and no blocking",
		"8": "dangerous condition",
		"9": "small aircraft operational defect"
	}

EVENT API
-----------------------------

Description:

	This will return a JSON object that has all of the incidents of a given event type id
	from a specific date.  Note you will need to get the ID's from the event type api json

Example API Call:

	http://monroe911.mycodespace.net/eventapi.php?eventtypeid=13&startdate=2012-1-1

Variables Passed In:

	eventtypeid - ID of the eventtype, note you must get this from the eventtypeapi.php api
	startdate - start date to return incidents from, in YYYY-MM-DD format

Example Results:

	[
		{
			"event":"Barking dogs",
			"address":"26 RIVERVIEW HT, Henrietta",
			"pubdate":"2013-01-10",
			"pubtime":"05:27:00",
			"status":"WAITING",
			"incidentid":"MCOP130100466",
			"scrapedatetime":"2013-01-10 05:28:00"
		},
		{
			"event":"Barking dogs",
			"address":"26 RIVERVIEW HT, Henrietta",
			"pubdate":"2013-01-10",
			"pubtime":"05:27:00",
			"status":"ENROUTE",
			"incidentid":"MCOP130100466",
			"scrapedatetime":"2013-01-10 05:29:00"
		}
	]

EVENT API
-----------------------------

Description:

	This returns a TSV file that can be used with D3 of the frequency of all event types since a date

Eample API Call:

	http://monroe911.mycodespace.net/statsapi.php?startdate=2013-1-11

Variables Passed In:

        startdate - start date to return incidents from, in YYYY-MM-DD format

Example Results:

	event	frequency
	A	122
	B	10
	C	120
	D	235 
	E	0 
	F	55 
	G	16 
	H	119 


