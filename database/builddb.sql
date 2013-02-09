create table incidents( incidentid int not null auto_increment primary key, event varchar(255) not null, address varchar(255) not null, pubdate date not null, pubtime time not null, status varchar(255) not null, itemid varchar(255) not null,scrapedatetime datetime not null);

create table runs (runid int not null auto_increment primary key, runsuccess bool not null, errtext text not null, rundatetime datetime not null);

create table blogposts ( blogpostid int not null auto_increment primary key, title text not null, body text not null, creationdate date not null);

create table apicalls (apicallid int not null auto_increment pirmary key, ipaddress varchar(255), calldatetime datetime not null, querytime float, api varchar(255));

create table currentincidents (currentincidentid int not null auto_increment primary key, incidentid int not null);

create table eventtypes (eventtypeid int not null auto_increment primary key, eventtype varchar(255)) not null;

create table statustypes (statustypeid int not null auto_incremenet primary key, statustype varchar(255) not null);