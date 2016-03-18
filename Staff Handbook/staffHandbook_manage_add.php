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

if (isActionAccessible($guid, $connection2, "/modules/Staff Handbook/staffHandbook_manage_add.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print "You do not have access to this action." ;
	print "</div>" ;
}
else {
	print "<div class='trail'>" ;
	print "<div class='trailHead'><a href='" . $_SESSION[$guid]["absoluteURL"] . "'>Home</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/" . getModuleEntry($_GET["q"], $connection2, $guid) . "'>" . getModuleName($_GET["q"]) . "</a> > <a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/" . getModuleName($_GET["q"]) . "/staffHandbook_manage.php'>" . __($guid, 'Manage Staff Handbook') . "</a> > </div><div class='trailEnd'>" . __($guid, 'Add Staff Handbook Entry') . "</div>" ;
	print "</div>" ;
	
	if (isset($_GET["addReturn"])) { $addReturn=$_GET["addReturn"] ; } else { $addReturn="" ; }
	$addReturnMessage ="" ;
	$class="error" ;
	if (!($addReturn=="")) {
		if ($addReturn=="fail0") {
			$addReturnMessage ="Add failed because you do not have access to this action." ;	
		}
		else if ($addReturn=="fail2") {
			$addReturnMessage ="Add failed due to a database error." ;	
		}
		else if ($addReturn=="fail3") {
			$addReturnMessage ="Add failed because your inputs were invalid." ;	
		}
		else if ($addReturn=="fail4") {
			$addReturnMessage ="Add failed because the selected person is already registered." ;	
		}
		else if ($addReturn=="fail5") {
			$addReturnMessage ="Add succeeded, but there were problems uploading one or more attachments." ;	
		}
		else if ($addReturn=="success0") {
			$addReturnMessage ="Add was successful. You can add another record if you wish." ;	
			$class="success" ;
		}
		print "<div class='$class'>" ;
			print $addReturnMessage;
		print "</div>" ;
	} 
	
	if ($_GET["search"]!="") {
		print "<div class='linkTop'>" ;
			print "<a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php?q=/modules/Staff Handbook/staffHandbook_manage.php&search=" . $_GET["search"] . "'>Back to Search Results</a>" ;
		print "</div>" ;
	}
	
	?>
	<form method="post" action="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/Staff Handbook/staffHandbook_manage_addProcess.php?search=" . $_GET["search"] ?>" enctype="multipart/form-data">
		<table class='smallIntBorder' cellspacing='0' style="width: 100%">	
			<tr>
				<td> 
					<b>Title *</b><br/>
				</td>
				<td class="right">
					<input name="title" id="title" maxlength=100 value="" type="text" style="width: 300px">
					<script type="text/javascript">
						var title=new LiveValidation('title');
						title.add(Validate.Presence);
					</script>
				</td>
			</tr>
			<tr>
				<td> 
					<b><?php print __($guid, 'Priority') ?> *</b><br/>
					<span style="font-size: 90%"><i><?php print __($guid, "Higher priorities are displayed first.") ?></i></span>
				</td>
				<td class="right">
					<input name="priority" id="priority" maxlength=2 value="0" type="text" style="width: 300px">
					<script type="text/javascript">
						var priority=new LiveValidation('priority');
						priority.add(Validate.Presence);
						priority.add(Validate.Numericality);
					</script>
				</td>
			</tr>	
			<tr>
				<td>
					<b><?php print __($guid, 'Link') ?> *</b><br/>
				</td>
				<td class='right'>
					<input name='url' id='url' maxlength=255 value='' type='text' style='width: 300px'>
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
					<span style="font-size: 90%"><i><?php print __($guid, '335px x 205px') ?></i></span>
				</td>
				<td class="right">
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
					<textarea name='logoLicense' id='logoLicense' rows=5 style='width: 300px'></textarea>
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
?>