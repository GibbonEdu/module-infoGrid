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


if (isActionAccessible($guid, $connection2, "/modules/Staff Handbook/staffHandbook_manage_edit.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print "You do not have access to this action." ;
	print "</div>" ;
}
else {
	//Proceed!
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>Home</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . getModuleName($_GET["q"]) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/staffHandbook_manage.php'>" . ___($guid, $guid, 'Manage Staff Handbook') . "</a> > </div><div class='trailEnd'>" . ___($guid, $guid, 'Edit Staff Handbook Entry') . "</div>" ;
	print "</div>" ;
	
	if (isset($_GET["updateReturn"])) { $updateReturn=$_GET["updateReturn"] ; } else { $updateReturn="" ; }
	$updateReturnMessage ="" ;
	$class="error" ;
	if (!($updateReturn=="")) {
		if ($updateReturn=="fail0") {
			$updateReturnMessage ="Update failed because you do not have access to this action." ;	
		}
		else if ($updateReturn=="fail1") {
			$updateReturnMessage ="Update failed because a required parameter was not set." ;	
		}
		else if ($updateReturn=="fail2") {
			$updateReturnMessage ="Update failed due to a database error." ;	
		}
		else if ($updateReturn=="fail3") {
			$updateReturnMessage ="Update failed because your inputs were invalid." ;	
		}
		else if ($updateReturn=="fail4") {
			$updateReturnMessage ="Update failed some values need to be unique but were not." ;	
		}
		else if ($updateReturn=="fail5") {
			$updateReturnMessage ="Update failed because your attachment could not be uploaded." ;	
		}
		else if ($updateReturn=="success0") {
			$updateReturnMessage ="Update was successful." ;	
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $updateReturnMessage;
		print "</div>" ;
	} 
	
	//Check if school year specified
	$staffHandbookEntryID=$_GET["staffHandbookEntryID"];
	if ($staffHandbookEntryID=="") {
		print "<div class='error'>" ;
			print "You have not specified a policy." ;
		print "</div>" ;
	}
	else {
		try {
			$data=array("staffHandbookEntryID"=>$staffHandbookEntryID);  
			$sql="SELECT * FROM staffHandbookEntry WHERE staffHandbookEntryID=:staffHandbookEntryID" ;
			$result=$connection2->prepare($sql);
			$result->execute($data);
		}
		catch(PDOException $e) { 
			print "<div class='error'>" . $e->getMessage() . "</div>" ; 
		}
		
		if ($result->rowCount()!=1) {
			print "<div class='error'>" ;
				print "The selected policy does not exist." ;
			print "</div>" ;
		}
		else {
			//Let's go!
			$row=$result->fetch() ;
			
			if ($_GET["search"]!="") {
				print "<div class='linkTop'>" ;
					print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Staff Handbook/staffHandbook_manage.php&search=" . $_GET["search"] . "'>Back to Search Results</a>" ;
				print "</div>" ;
			}
			?>
			<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/Staff Handbook/staffHandbook_manage_editProcess.php?staffHandbookEntryID=$staffHandbookEntryID&search=" . $_GET["search"] ?>" enctype="multipart/form-data">
				<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
					<tr>
						<td> 
							<b>Title *</b><br/>
						</td>
						<td class="right">
							<input name="title" id="title" maxlength=100 value="<?php print htmlPrep($row["title"]) ?>" type="text" style="width: 300px">
							<script type="text/javascript">
								var title=new LiveValidation('title');
								title.add(Validate.Presence);
							</script>
						</td>
					</tr>
					<tr>
						<td> 
							<b><?php print ___($guid, $guid, 'Priority') ?> *</b><br/>
							<span style="font-size: 90%"><i><?php print ___($guid, $guid, "Higher priorities are displayed first.") ?></i></span>
						</td>
						<td class="right">
							<input name="priority" id="priority" maxlength=2 value="<?php print htmlPrep($row["priority"]) ?>" type="text" style="width: 300px">
							<script type="text/javascript">
								var priority=new LiveValidation('priority');
								priority.add(Validate.Presence);
								priority.add(Validate.Numericality);
							</script>
						</td>
					</tr>	
					<tr>
						<td>
							<b><?php print ___($guid, $guid, 'Link') ?> *</b><br/>
						</td>
						<td class='right'>
							<input name='url' id='url' maxlength=255 value='<?php print htmlPrep($row["url"]) ?>' type='text' style='width: 300px'>
							<script type='text/javascript'>
								url=new LiveValidation('url');
								url.add(Validate.Presence);
								url.add( Validate.Format, { pattern: /(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/, failureMessage: 'Must start with http://' } );
							</script>	
						</td>
					</tr>
					<tr>
						<td> 
							<b>Logo</b><br/>
							<span style="font-size: 90%"><i><?php print ___($guid, $guid, '240px x 240px') . "<br/>" ?>
							<?php if ($row["logo"]!="") {
								print ___($guid, $guid, 'Will overwrite existing attachment.') ;
							} ?>
							</i></span>
						</td>
						<td class="right">
							<?php
							if ($row["logo"]!="") {
								print ___($guid, $guid, "Current attachment:") . " <a target='_blank' href='" . $_SESSION[$guid]["absoluteURL"] . "/" . $row["logo"] . "'>" . $row["logo"] . "</a><br/><br/>" ;
							}
							?>
							<input type="file" name="file" id="file">
							<script type="text/javascript">
								var file=new LiveValidation('file');
								file.add( Validate.Inclusion, { within: ['gif','jpg','jpeg','png'], failureMessage: "Illegal file type!", partialMatch: true, caseSensitive: false } );
							</script>
						</td>
					</tr>
					<tr>
						<td> 
							<b>Logo License/Credits</b><br/>
						</td>
						<td class="right">
							<textarea name='logoLicense' id='logoLicense' rows=5 style='width: 300px'><?php print htmlPrep($row["logoLicense"]) ?></textarea>
						</td>
					</tr>
					
					
					<tr>
						<td>
							<span style="font-size: 90%"><i>* denotes a required field</i></span>
						</td>
						<td class="right">
							<input type="hidden" name="address" value="<?php print $_SESSION[$guid]["address"] ?>">
							<input type="submit" value="Submit">
						</td>
					</tr>
				</table>
			</form>
			<?php
		}
	}
}
?>