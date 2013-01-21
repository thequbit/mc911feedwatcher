create table incidents( incidentid int not null auto_increment primary key, event varchar(255) not null, address varchar(255) not null, pubdate date not null, pubtime time not null, status varchar(255) not null, itemid varchar(255) not null,scrapedatetime datetime not null);

create table runs (runid int not null auto_increment primary key, runsuccess bool not null, errtext text not null, rundatetime datetime not null);
