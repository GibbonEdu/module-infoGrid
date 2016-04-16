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
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

@session_start() ;

//Module includes
include "./modules/" . $_SESSION[$guid]["module"] . "/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Staff Handbook/staffHandbook_view.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print ___($guid, $guid, "You do not have access to this action.") ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>" . ___($guid, $guid, "Home") . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . ___($guid, $guid, getModuleName($_GET["q"])) . "</a> > </div><div class='trailEnd'>" . ___($guid, $guid, 'View Staff Handbook') . "</div>" ;
	print "</div>" ;
	
	//Get action with highest precendence
	$highestAction=getHighestGroupedAction($guid, $_GET["q"], $connection2) ;
	if ($highestAction==FALSE) {
		print "<div class='error'>" ;
		print ___($guid, $guid, "The highest grouped action cannot be determined.") ;
		print "</div>" ;
	}
	else {
		print "<h2>" ;
		print ___($guid, $guid, "Staff Handbook") ;
		print "</h2>" ;
		print getStaffHandbook($connection2, $guid) ;
	}
}
?>