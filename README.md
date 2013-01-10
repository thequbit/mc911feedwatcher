mc911feedwatcher
================

Pulls the XML of Monroe County's 911 feed and provides a web API 

Scraper runs every 60 seconds and pushes only unique Incident ID's and Incident Status's to the DB.

Next to come is a nice little web API that will host up any data you want.

Items saved:

+----------------+--------------+------+-----+---------+----------------+
| Field          | Type         | Null | Key | Default | Extra          |
+----------------+--------------+------+-----+---------+----------------+
| incidentid     | int(11)      | NO   | PRI | NULL    | auto increment |
| event          | varchar(255) | NO   |     | NULL    |                |
| address        | varchar(255) | NO   |     | NULL    |                |
| pubdate        | date         | NO   |     | NULL    |                |
| pubtime        | time         | NO   |     | NULL    |                |
| status         | varchar(255) | NO   |     | NULL    |                |
| itemid         | varchar(255) | NO   |     | NULL    |                |
| scrapedatetime | datetime     | NO   |     | NULL    |                |
+----------------+--------------+------+-----+---------+----------------+



