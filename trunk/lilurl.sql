-- MySQL dump 8.22
--
-- Host: localhost    Database: lilurl
---------------------------------------------------------
-- Server version	3.23.57

--
-- Table structure for table 'lil_urls'
--


CREATE TABLE lil_urls (
  id varchar(255) NOT NULL default '',
  url varchar(255) NOT NULL,
  manual enum('false','true') NOT NULL default 'false' COMMENT 'true if id was chosen by user',
  date timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table 'lil_urls'
--



