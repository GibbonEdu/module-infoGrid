<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

include "../../functions.php" ;
include "../../config.php" ;

include "./moduleFunctions.php" ;

//New PDO DB connection
try {
    $connection2=new PDO("mysql:host=$databaseServer;dbname=$databaseName", $databaseUsername, $databasePassword);
	$connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
    echo $e->getMessage();
}


@session_start() ;

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

$URL=$_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_POST["address"]) . "/staffHandbook_manage_add.php&search=" . $_GET["search"] ;

if (isActionAccessible($guid, $connection2, "/modules/Staff Handbook/staffHandbook_manage_add.php")==FALSE) {
	//Fail 0
	$URL=$URL . "&addReturn=fail0" ;
	header("Location: {$URL}");
}
else {
	//Proceed!
	$title=$_POST["title"] ;
	$priority=$_POST["priority"] ;
	$url=$_POST["url"] ;
	$logoLicense=$_POST["logoLicense"] ;
	
	if ($title=="" OR $priority=="" OR $url=="") {
		//Fail 3
		$URL=$URL . "&addReturn=fail3" ;
		header("Location: {$URL}");
	}
	else {
		$partialFail=FALSE ;
		$logo=NULL ;
		if ($_FILES['file']["tmp_name"]!="") {
			//Attempt file upload
			$time=time() ;
				
			//Check for folder in uploads based on today's date
			$path=$_SESSION[$guid]["absolutePath"] ; ;
			if (is_dir($path ."/uploads/" . date("Y", $time) . "/" . date("m", $time))==FALSE) {
				mkdir($path ."/uploads/" . date("Y", $time) . "/" . date("m", $time), 0777, TRUE) ;
			}
			$unique=FALSE;
			while ($unique==FALSE) {
				$suffix=randomPassword(16) ;
				$logo="uploads/" . date("Y", $time) . "/" . date("m", $time) . "/staffHandbook_$suffix" . strrchr($_FILES["file"]["name"], ".") ;
				if (!(file_exists($path . "/" . $logo))) {
					$unique=TRUE ;
				}
			}
		
			if (!(move_uploaded_file($_FILES["file"]["tmp_name"],$path . "/" . $logo))) {
				//Fail 5
				$URL=$URL . "&addReturn=fail5" ;
				header("Location: {$URL}");
			}
		}
		
		if ($partialFail==TRUE) {
			//Fail 5
			$URL=$URL . "&addReturn=fail5" ;
			header("Location: {$URL}");
			exit() ;
		}
		else {
			//Write to database
			try {
				$data=array("title"=>$title, "priority"=>$priority, "url"=>$url, "logo"=>$logo, "logoLicense"=>$logoLicense, "gibbonPersonIDCreator"=>$_SESSION[$guid]["gibbonPersonID"], "timestampCreated"=>date("Y-m-d H:i:s"));  
				$sql="INSERT INTO staffHandbookEntry SET title=:title, priority=:priority, url=:url, logo=:logo, logoLicense=:logoLicense, gibbonPersonIDCreator=:gibbonPersonIDCreator, timestampCreated=:timestampCreated" ;
				$result=$connection2->prepare($sql);
				$result->execute($data);  
			}
			catch(PDOException $e) {
				print $e->getMessage() ; exit() ;
				//Fail 2
				$URL=$URL . "&addReturn=fail2" ;
				header("Location: {$URL}");
				exit() ;
			}

			//Success 0
			$URL=$URL . "&addReturn=success0" ;
			header("Location: {$URL}");
		}
	}
}
?>