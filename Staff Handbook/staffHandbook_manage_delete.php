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

@session_start();

//Module includes
include './modules/Staff Handbook/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Staff Handbook/staffHandbook_manage_delete.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo 'You do not have access to this action.';
    echo '</div>';
} else {
    //Proceed!
    echo "<div class='trail'>";
    echo "<div class='trailHead'><a href='".$_SESSION[$guid]['absoluteURL']."'>Home</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q']).'/'.getModuleEntry($_GET['q'], $connection2, $guid)."'>".getModuleName($_GET['q'])."</a> > <a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_GET['q'])."/staffHandbook_manage.php'>".__($guid, 'Manage Staff Handbook')."</a> > </div><div class='trailEnd'>".__($guid, 'Delete Staff Hanbook Entry').'</div>';
    echo '</div>';

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    //Check if school year specified
    $staffHandbookEntryID = $_GET['staffHandbookEntryID'];
    if ($staffHandbookEntryID == '') {
        echo "<div class='error'>";
        echo 'You have not specified a policy.';
        echo '</div>';
    } else {
        try {
            $data = array('staffHandbookEntryID' => $staffHandbookEntryID);
            $sql = 'SELECT * FROM staffHandbookEntry WHERE staffHandbookEntryID=:staffHandbookEntryID';
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            echo "<div class='error'>".$e->getMessage().'</div>';
        }

        if ($result->rowCount() != 1) {
            echo "<div class='error'>";
            echo 'The selected policy does not exist.';
            echo '</div>';
        } else {
            //Let's go!
            $row = $result->fetch();

            if ($_GET['search'] != '') {
                echo "<div class='linkTop'>";
                echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Staff Handbook/staffHandbook_manage.php&search='.$_GET['search']."'>Back to Search Results</a>";
                echo '</div>';
            }
            ?>
			<form method="post" action="<?php echo $_SESSION[$guid]['absoluteURL']."/modules/Staff Handbook/staffHandbook_manage_deleteProcess.php?staffHandbookEntryID=$staffHandbookEntryID&search=".$_GET['search'] ?>">
				<table class='smallIntBorder' cellspacing='0' style="width: 100%">
					<tr>
						<td>
							<b>Are you sure you want to delete "<?php echo $row['title'] ?>" from Staff Handbook?</b><br/>
							<span style="font-size: 90%; color: #cc0000"><i>This operation cannot be undone, and may lead to loss of vital data in your system.<br/>PROCEED WITH CAUTION!</i></span>
						</td>
						<td class="right">

						</td>
					</tr>
					<tr>
						<td>
							<input name="staffHandbookEntryID" id="staffHandbookEntryID" value="<?php echo $staffHandbookEntryID ?>" type="hidden">
							<input type="hidden" name="address" value="<?php echo $_SESSION[$guid]['address'] ?>">
							<input type="submit" value="Yes">
						</td>
						<td class="right">

						</td>
					</tr>
				</table>
			</form>
			<?php

        }
    }
}
?>
