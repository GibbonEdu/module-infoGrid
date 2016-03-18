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

function getStaffHandbook($connection2, $guid) {
	$output="" ;
	
	try {
		$data=array();  
		$sql="SELECT staffHandbookEntry.* FROM staffHandbookEntry ORDER BY priority DESC, title" ; 
		$result=$connection2->prepare($sql);
		$result->execute($data);
	}
	catch(PDOException $e) { 
		$output.="<div class='error'>" . $e->getMessage() . "</div>" ; 
	}
	
	if ($result->rowCount()<1) {
		$output.="<div class='error'>" ;
		$output.=__($guid, "There are no records to display.") ;
		$output.="</div>" ;
	}
	else {
		$count=0 ;
		
		$output.="<table class='noIntBorder' cellspacing='0' style='width: 100%'>" ;
			while ($row=$result->fetch()) {
				if ($count%2==0) {
					$output.="<tr>" ;
				}
				
				$output.="<td style='text-align: center; width: 50%'>" ;
					$output.="<div style='margin: 10px 0'>" ;
						$output.="<a style='font-size: 150%; font-weight: bold; letter-spacing: -0.5px;' href='" . $row["url"] . "' target='_blank'>" ;
							if ($row["logo"]!="") {
								$output.="<img class='user' style='margin-bottom: 10px; width: 335px; height: 205px' src='" . $_SESSION[$guid]["absoluteURL"] . "/" . $row["logo"] . "'/>" ;
							}
							else {
								$output.="<img class='user' style='margin-bottom: 10px; width: 335px; height: 205px' src='" . $_SESSION[$guid]["absoluteURL"] . "/modules/Staff Handbook/img/anonymous.jpg'/>" ;
							}
							$output.=$row["title"] ;
						$output.="</a>" ;
					$output.="</div>" ;
				$output.="</td>" ;
				
				if ($count%2==1) {
					$output.="</tr>" ;
				}
				
				$count++ ;
			}
			if ($count%2==1) {
				$output.="<td style='text-align: center; width: 50%'></td></tr>" ;
			}
		$output.="</table>" ;
	}
	
	return $output ;
}

?>
