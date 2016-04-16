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

@session_start() ;

//Module includes
include "./modules/Staff Handbook/moduleFunctions.php" ;

if (isActionAccessible($guid, $connection2, "/modules/Staff Handbook/staffHandbook_manage.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print "You do not have access to this action." ;
	print "</div>" ;
}
else {
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>Home</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . getModuleName($_GET["q"]) . "</a> > </div><div class='trailEnd'>Manage Staff Handbook</div>" ;
	print "</div>" ;
	
	if (isset($_GET["deleteReturn"])) { $deleteReturn=$_GET["deleteReturn"] ; } else { $deleteReturn="" ; }
	$deleteReturnMessage ="" ;
	$class="error" ;
	if (!($deleteReturn=="")) {
		if ($deleteReturn=="success0") {
			$deleteReturnMessage ="Delete was successful." ;	
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $deleteReturnMessage;
		print "</div>" ;
	}
	
	//Set pagination variable
	$page=NULL ;
	if (isset($_GET["page"])) {
		$page=$_GET["page"] ;
	}
	if ((!is_numeric($page)) OR $page<1) {
		$page=1 ;
	}
	
	$search=NULL ;
	if (isset($_GET["search"])) {
		$search=$_GET["search"] ;
	}
	
	print "<h2 class='top'>" ;
	print "Search" ;
	print "</h2>" ;
	?>
	<form method="get" action="<?php print $_SESSION[$guid]["absoluteURL"]?>/index.php">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
			<tr>
				<td> 
					<b>Search For</b><br/>
					<span style="font-size: 90%"><i>Title</i></span>
				</td>
				<td class="right">
					<input name="search" id="search" maxlength=20 value="<?php print $search ?>" type="text" style="width: 300px">
				</td>
			</tr>
			<tr>
				<td colspan=2 class="right">
					<input type="hidden" name="q" value="/modules/<?php print $_SESSION[$guid]["module"] ?>/staffHandbook_manage.php">
					<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
					<?php
					print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . $_SESSION[$guid]["module"] . "/staffHandbook_manage.php'>Clear Search</a> " ;
					?>
					<input type="submit" value="Submit">
				</td>
			</tr>
		</table>
	</form>
	
	<?php
	print "<h2 class='top'>" ;
	print "View" ;
	print "</h2>" ;
	
	try {
		$data=array();  
		$sql="SELECT staffHandbookEntry.* FROM staffHandbookEntry ORDER BY priority DESC, title" ; 
		if ($search!="") {
			$data=array("search1"=>"%$search%"); 
			$sql="SELECT staffHandbookEntry.* FROM staffHandbookEntry WHERE staffHandbookEntry.title LIKE :search1 ORDER BY priority DESC, title" ; 
		}
		$sqlPage= $sql . " LIMIT " . $_SESSION[$guid]["pagination"] . " OFFSET " . (($page-1)*$_SESSION[$guid]["pagination"]) ;
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) { 
		print "<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
	
	print "<div class='linkTop'>" ;
	print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Staff Handbook/staffHandbook_manage_add.php&search=$search'>" .  ___($guid, $guid, 'Add') . "<img style='margin-left: 5px' title='" . ___($guid, $guid, 'Add') . "' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/page_new.png'/></a>" ;
	print "</div>" ;
		
	if ($result->rowCount()<1) {
		print "<div class='error'>" ;
		print "There are no records to display." ;
		print "</div>" ;
	}
	else {
		if ($result->rowCount()>$_SESSION[$guid]["pagination"]) {
			printPagination($guid, $result->rowCount(), $page, $_SESSION[$guid]["pagination"], "top", "search=$search") ;
		}
	
		print "<table cellspacing='0' style='width: 100%'>" ;
			print "<tr class='head'>" ;
				print "<th style='width: 180px'>" ;
					print ___($guid, $guid, "Logo") ;
				print "</th>" ;
				print "<th>" ;
					print "Name<br/>" ;
				print "</th>" ;
				print "<th>" ;
					print "Priority<br/>" ;
				print "</th>" ;
				print "<th style='width: 120px'>" ;
					print "Actions" ;
				print "</th>" ;
			print "</tr>" ;
			
			$count=0;
			$rowNum="odd" ;
			try {
				$resultPage=$connection2->prepare($sqlPage);
				$resultPage->execute($data);
			}
			catch(PDOException $e) { 
				print "<div class='error'>" . $e->getMessage() . "</div>" ; 
			}
			while ($row=$resultPage->fetch()) {
				if ($count%2==0) {
					$rowNum="even" ;
				}
				else {
					$rowNum="odd" ;
				}
				$count++ ;
				
				//COLOR ROW BY STATUS!
				print "<tr class=$rowNum>" ;
					print "<td>" ;
						if ($row["logo"]!="") {
							print "<img class='user' style='width: 335px; height: 140px' src='" . $_SESSION[$guid]["absoluteURL"] . "/" . $row["logo"] . "'/>" ;
						}
						else {
							print "<img class='user' style='width: 335px; height: 140px' src='" . $_SESSION[$guid]["absoluteURL"] . "/modules/Staff Handbook/img/anonymous.jpg'/>" ;
						}
					print "</td>" ;
					print "<td>" ;
						print "<a href='" . $row["url"] . "'>" . $row["title"] . "</a>" ;
					print "</td>" ;
					print "<td>" ;
						print $row["priority"] ;
					print "</td>" ;
					print "<td>" ;
						print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Staff Handbook/staffHandbook_manage_edit.php&staffHandbookEntryID=" . $row["staffHandbookEntryID"] . "&search=$search'><img title='Edit' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/config.png'/></a> " ;
						print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Staff Handbook/staffHandbook_manage_delete.php&staffHandbookEntryID=" . $row["staffHandbookEntryID"] . "&search=$search'><img title='Delete' src='./themes/" . $_SESSION[$guid]["gibbonThemeName"] . "/img/garbage.png'/></a> " ;
					print "</td>" ;
				print "</tr>" ;
			}
		print "</table>" ;
		
		if ($result->rowCount()>$_SESSION[$guid]["pagination"]) {
			printPagination($guid, $result->rowCount(), $page, $_SESSION[$guid]["pagination"], "bottom", "search=$search") ;
		}
	}

}	
?>