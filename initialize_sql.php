<?php
	$SQL_CREATE_TABLE_POST = 
		"CREATE TABLE IF NOT EXISTS ".$dbname.".`POST` (".
		"`P_ID` INT NOT NULL AUTO_INCREMENT,".
		"`T_ID` INT NOT NULL,".
		"`P_NAME` VARCHAR(64) NOT NULL,".
		"`P_TEXT` TEXT NOT NULL,".
		"`P_POSTTIME` TIMESTAMP NOT NULL,".
		"`P_EMAIL` VARCHAR(128) NULL,".
		"`POSTcol` VARCHAR(45) NOT NULL,".
		"PRIMARY KEY (`P_ID`),".
		"UNIQUE INDEX `P_ID_UNIQUE` (`P_ID` ASC))".
		"ENGINE = InnoDB";
	$SQL_CREATE_TABLE_THREAD = 
		"CREATE TABLE IF NOT EXISTS ".$dbname.".`THREAD` (".
		" `T_ID` INT NOT NULL AUTO_INCREMENT,".
		"`T_TITLE` VARCHAR(128) NOT NULL,".
		"`T_MAKEDATE` DATETIME NOT NULL,".
		"`T_UPDATE` TIMESTAMP NOT NULL,".
		"`T_RES` INT NOT NULL,".
		"PRIMARY KEY (`T_ID`),".
		"UNIQUE INDEX `THREAD_ID_UNIQUE` (`T_ID` ASC))".
		"ENGINE = InnoDB";
	$SQL_SELECT_THREAD_DUMMY = 
		"SELECT * FROM THREAD WHERE T_ID = 1;";
	$SQL_INSERT_THREAD_DUMMY = 
		"INSERT INTO THREAD (T_ID,T_TITLE, T_MAKEDATE)".
		"VALUES (1,'DUMMY','0000-00-00 00:00:00')";

?>