-- $Id: tabcreate.sql.in,v 1.2.2.7 2004/10/06 13:58:35 tbaron Exp $
-- Create tables for the CDS Software.

-- This file is part of CDS Agenda..
-- Copyright (C) 2002 CERN.
--
-- The CDS Agenda Software is free software; you can redistribute it and/or
-- modify it under the terms of the GNU General Public License as
-- published by the Free Software Foundation; either version 2 of the
-- License, or (at your option) any later version.
--
-- The CDS Agenda Software is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
-- General Public License for more details.  
--
-- You should have received a copy of the GNU General Public License
-- along with CDS Agenda Software; if not, write to the Free Software
-- Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.

--
-- table for managing the hierarchy:
--

CREATE TABLE IF NOT EXISTS LEVEL (
  uid mediumint(8) unsigned NOT NULL auto_increment,
  fid varchar(5) NOT NULL default '',
  level int(11) NOT NULL default '0',
  title varchar(250) NOT NULL default '',
  cd date NOT NULL default '2000-01-01',
  md date NOT NULL default '2000-01-01',
  abstract text,
  icon varchar(50) default NULL,
  stitle varchar(100) default NULL,
  visibility int(4) NOT NULL default 999,
  modifyPassword varchar(15) NOT NULL default '',
  accessPassword varchar(15) NOT NULL default '',
  categorder int(4) NOT NULL default 0,
  KEY fid (fid),
  KEY uid (uid)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS COUNTER (
  name char(20) NOT NULL default '',
  num int(11) default NULL,
  PRIMARY KEY  (name)
) TYPE=MyISAM;

--
-- tables for describing the agendas
--

CREATE TABLE IF NOT EXISTS AGENDA (
  title varchar(255) NOT NULL default '',
  id varchar(6) NOT NULL default '',
  stdate date default '1998-01-01',
  endate date default '1998-01-01',
  location varchar(50) default NULL,
  chairman varchar(50) default NULL,
  cem varchar(50) default NULL,
  status varchar(6) default NULL,
  an varchar(15) default NULL,
  cd date default '1998-01-01',
  md date default '1998-01-01',
  stylesheet varchar(50) default 'standard',
  format varchar(10) default NULL,
  confidentiality varchar(15) default NULL,
  apassword varchar(15) default NULL,
  repno varchar(20) default NULL,
  fid varchar(5) NOT NULL default '',
  acomments text NOT NULL,
  keywords varchar(100) default NULL,
  visibility varchar(5) default NULL,
  bld varchar(30) default '',
  floor char(2) default NULL,
  room varchar(80) default NULL,
  nbsession int(11) NOT NULL default '0',
  stime time default '08:00:00',
  etime time default '12:00:00',
  PRIMARY KEY  (id)
) TYPE=MyISAM;


CREATE TABLE IF NOT EXISTS SESSION (
  ida varchar(6) NOT NULL default '',
  ids varchar(5) default NULL,
  schairman varchar(50) default NULL,
  speriod1 date default NULL,
  stime time default NULL,
  eperiod1 date default NULL,
  etime time default NULL,
  stitle varchar(50) default NULL,
  snbtalks int(11) default NULL,
  slocation varchar(50) default NULL,
  scem varchar(50) default '',
  sstatus varchar(5) default NULL,
  bld varchar(30) default '',
  floor char(2) default NULL,
  room varchar(80) default '',
  broadcasturl varchar(100) default NULL,
  cd date default NULL,
  md date default NULL,
  scomment text,
  email varchar(50) default '',
  KEY ida (ida)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS TALK (
  ida varchar(6) NOT NULL default '',
  ids varchar(5) NOT NULL default '',
  idt varchar(5) default NULL,
  ttitle varchar(200) default NULL,
  tspeaker varchar(100) default NULL,
  tday date default NULL,
  tcomment text,
  location varchar(50) default NULL,
  bld varchar(30) default '',
  floor char(3) default NULL,
  room varchar(80) default '',
  broadcasturl varchar(100) default NULL,
  type int(11) default NULL,
  cd date default NULL,
  md date default NULL,
  category varchar(200) default NULL,
  stime time NOT NULL default '00:00:00',
  repno varchar(100) default NULL,
  affiliation varchar(100) default NULL,
  duration time NOT NULL default '00:00:00',
  keywords text,
  email varchar(50) default '',
  KEY ida (ida),
  KEY ids (ids)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS SUBTALK (
  ida varchar(6) NOT NULL default '',
  ids varchar(5) NOT NULL default '',
  idt varchar(5) default NULL,
  ttitle varchar(200) default NULL,
  tspeaker varchar(100) default NULL,
  tday date default NULL,
  tcomment text,
  type int(11) default NULL,
  cd date default NULL,
  md date default NULL,
  stime time NOT NULL default '00:00:00',
  repno varchar(100) default NULL,
  affiliation varchar(100) default NULL,
  duration time NOT NULL default '00:00:00',
  fidt varchar(5) NOT NULL default '',
  email varchar(50) default '',
  KEY ida (ida),
  KEY ids (ids)
) TYPE=MyISAM;

--
-- alert management
--

CREATE TABLE IF NOT EXISTS ALERT (
  id varchar(10) NOT NULL default '',
  address varchar(150) NOT NULL default '',
  delay tinyint(4) NOT NULL default '0',
  text text NOT NULL,
  include_agenda varchar(5) NOT NULL default 'off'
) TYPE=MyISAM;

--
-- archive management
--

CREATE TABLE IF NOT EXISTS CATEGORY (
  categoryID int(4) unsigned NOT NULL auto_increment,
  shortName varchar(255) NOT NULL default '',
  longName varchar(255) NOT NULL default '',
  description varchar(255) NOT NULL default '',
  preselected tinyint(1) default NULL,
  PRIMARY KEY  (categoryID)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS FILE (
  id int(12) unsigned NOT NULL auto_increment,
  format varchar(50) default NULL,
  category varchar(50) default NULL,
  cd DATETIME,
  md DATETIME,
  name varchar(255) NOT NULL default '',
  path varchar(255) default NULL,
  description varchar(255) default '',
  numPages int(5) default '0',
  size int(12) unsigned default '0',
  deleted tinyint(1) default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS FILETRASH (
  id int(12) unsigned NOT NULL default '',
  deletionDate DATETIME,
  deletedName varchar(255) NOT NULL default '',
  deletedPath varchar(255) default NULL,
  PRIMARY KEY (id)
) TYPE=MyISAM;
  
CREATE TABLE IF NOT EXISTS FILE_DOMAIN (
  fileID int(12) default '0',
  domainIP varchar(255) default ''
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS FILE_PASSWORD (
  fileID int(12) default '0',
  password varchar(255) default ''
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS FILE_EVENT (
  fileID int(12) default '0',
  eventID varchar(20) NOT NULL default ''
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS FILE_USER (
  fileID int(12) default '0',
  userID int(12) default ''
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS EVENT_PASSWORD (
  eventID varchar(20) NOT NULL default '',
  password varchar(255) default ''
) TYPE=MyISAM;

--
-- Statistics
--

CREATE TABLE IF NOT EXISTS ACCESSSTATS (
  ID int(13) NOT NULL auto_increment,
  userORevent varchar(80) NOT NULL default '',
  failed tinyint(1) default '0',
  wrongPassword varchar(80) default NULL,
  time varchar(30) default NULL,
  remoteAddress varchar(120) default NULL,
  browserType varchar(255) default NULL,
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS DOCUMENTSTATS (
  ID int(20) NOT NULL auto_increment,
  originalDataID int(12) unsigned default '0',
  actualDataID int(13) unsigned default '0',
  time varchar(30) default NULL,
  remoteAddress varchar(80) default NULL,
  downSize int(12) default '0',
  browserType varchar(200) default '',
  ownerUserID int(6) unsigned default '0',
  fromType varchar(80) default '',
  PRIMARY KEY  (ID)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS EVENTHITSTATS (
  id int(15) NOT NULL auto_increment,
  time varchar(30) default NULL,
  remoteAddress varchar(120) default NULL,
  browserType varchar(255) default NULL,
  eventID varchar(30) default NULL,
  userID int(6) unsigned default NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

--
-- manager back up tables (agenda's trash)
--

CREATE TABLE IF NOT EXISTS BK_AGENDA (
  title varchar(150) NOT NULL default '',
  id varchar(10) NOT NULL default '',
  stdate date default '1998-01-01',
  endate date default '1998-01-01',
  location varchar(50) default NULL,
  nbsession int(11) default NULL,
  chairman varchar(50) default NULL,
  cem varchar(50) default NULL,
  status varchar(6) default NULL,
  an varchar(15) default NULL,
  cd date default '1998-01-01',
  md date default '1998-01-01',
  dd datetime default '1998-01-01 00:00:00',
  stylesheet varchar(50) default 'standard',
  format varchar(10) default NULL,
  confidentiality varchar(15) default NULL,
  apassword varchar(15) default NULL,
  repno varchar(20) default NULL,
  fid varchar(10) NOT NULL default '',
  acomments text NOT NULL,
  keywords varchar(100) NOT NULL default '',
  visibility varchar(5) NOT NULL default '',
  bld int(11) NOT NULL default '0',
  floor char(2) NOT NULL default '',
  room varchar(20) NOT NULL default '',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS BK_TALK (
  ida varchar(10) NOT NULL default '',
  ids varchar(5) NOT NULL default '',
  idt varchar(5) default NULL,
  ttitle varchar(200) default NULL,
  tspeaker varchar(100) default NULL,
  tday date default NULL,
  tcomment text,
  location varchar(50) default NULL,
  bld int(11) default NULL,
  floor char(3) default NULL,
  room varchar(20) default NULL,
  broadcasturl varchar(100) default NULL,
  type int(11) default NULL,
  cd date default NULL,
  md date default NULL,
  category varchar(200) default NULL,
  stime time NOT NULL default '00:00:00',
  repno varchar(100) default NULL,
  affiliation varchar(100) default NULL,
  duration time NOT NULL default '00:00:00',
  email varchar(50) NOT NULL default '',
  KEY ida (ida),
  KEY ids (ids),
  KEY tspeaker (tspeaker)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS BK_SESSION (
  ida varchar(10) NOT NULL default '',
  ids varchar(5) default NULL,
  schairman varchar(50) default NULL,
  speriod1 date default NULL,
  stime time NOT NULL default '00:00:00',
  eperiod1 date default NULL,
  etime time default NULL,
  stitle varchar(100) default NULL,
  snbtalks int(11) default NULL,
  slocation varchar(50) default NULL,
  scem varchar(50) default NULL,
  sstatus varchar(5) default NULL,
  bld int(11) default NULL,
  floor char(2) default NULL,
  room varchar(20) default NULL,
  broadcasturl varchar(100) default NULL,
  cd date default NULL,
  md date default NULL,
  scomment text,
  email varchar(50) default '',
  KEY ida (ida)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS BK_SUBTALK (
  ida varchar(10) NOT NULL default '',
  ids varchar(5) NOT NULL default '',
  idt varchar(5) default NULL,
  ttitle varchar(200) default NULL,
  tspeaker varchar(100) default NULL,
  tday date default NULL,
  tcomment text,
  type int(11) default NULL,
  cd date default NULL,
  md date default NULL,
  stime time NOT NULL default '00:00:00',
  repno varchar(100) default NULL,
  affiliation varchar(100) default NULL,
  duration time NOT NULL default '00:00:00',
  fidt varchar(5) NOT NULL default '',
  email varchar(50) NOT NULL default '',
  KEY ida (ida),
  KEY ids (ids)
) TYPE=MyISAM;

--
-- table used by platform/database system
--

CREATE TABLE IF NOT EXISTS cdsagenda (
  tablename text,
  fields2 text,
  types text,
  sequences text
) TYPE=MyISAM;

--
-- authentication
--

CREATE TABLE IF NOT EXISTS auth_session (
  session_key varchar(32) NOT NULL default '',
  session_vars text NOT NULL,
  session_expiry int(11) unsigned NOT NULL default '0',
  UNIQUE KEY session_key (session_key)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS authorization (
  id int(4) unsigned NOT NULL auto_increment,
  name varchar(50) NOT NULL default '',
  description text,
  itemType varchar(20) NOT NULL default '',
  PRIMARY KEY (id),
  UNIQUE KEY name (name)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS user (
  id int(15) unsigned NOT NULL auto_increment,
  email varchar(255) NOT NULL default '',
  password varchar(20) default NULL,
  note varchar(255) default NULL,
  settings varchar(255) default NULL,
  UNIQUE KEY id (id),
  KEY email (email)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS groups (
  id int(4) unsigned NOT NULL auto_increment,
  name varchar(30) NOT NULL default '',
  description text,
  special varchar(50) NOT NULL default '',
  PRIMARY KEY (id),
  UNIQUE KEY name (name)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS user_groups (
  userID int(15) unsigned NOT NULL,
  groupID int(4) unsigned NOT NULL auto_increment,
  PRIMARY KEY id (userID,groupID)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS role (
  id int(4) unsigned NOT NULL auto_increment,
  name varchar(30) NOT NULL default '',
  description text,
  PRIMARY KEY  (id),
  UNIQUE KEY (name)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS user_authorization_item (
  userID int(15) unsigned NOT NULL,
  authID int(4) unsigned NOT NULL,
  itemID varchar(20) NOT NULL default '',
  recursive int(1) default 1,
  PRIMARY KEY id (userID,authID,itemID)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS group_authorization_item (
  groupID int(15) unsigned NOT NULL,
  authID int(4) unsigned NOT NULL,
  itemID varchar(20) NOT NULL default '',
  recursive int(1) default 1,
  PRIMARY KEY id (groupID,authID,itemID)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS ACTUALROLES (
  roleID int(4) unsigned NOT NULL default '0',
  eventID varchar(20) NOT NULL default '',
  userID int(6) unsigned NOT NULL default '0',
  fromDate int(12) unsigned default '0',
  toDate int(12) unsigned default '0',
  propriety int(16) unsigned default '0',
  recursive tinyint(1) NOT NULL default '0',
  disabled tinyint(1) NOT NULL default '0',
  addedByUserID int(12) unsigned default NULL,
  notify tinyint(1) default '1',
  PRIMARY KEY  (userID,roleID,eventID)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS DEFAULTPOLICIES (
  roleID int(4) unsigned NOT NULL default '0',
  eventTypeID int(4) unsigned NOT NULL default '0',
  propriety int(16) unsigned default '0',
  recursive tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (eventTypeID,roleID)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS DEFAULTGROUPPOLICIES (
  groupID int(4) unsigned NOT NULL default '0',
  eventTypeID int(4) unsigned NOT NULL default '0',
  propriety int(32) unsigned default '0',
  PRIMARY KEY  (groupID,eventTypeID)
) TYPE=MyISAM;

CREATE TABLE IF NOT EXISTS ROLES (
  roleID int(4) unsigned NOT NULL auto_increment,
  description text,
  recursive tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (roleID)
) TYPE=MyISAM;


-- end of file
