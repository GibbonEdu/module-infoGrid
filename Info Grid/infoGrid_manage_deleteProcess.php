<?php
/*
Gibbon: the flexible, open school platform
Founded by Ross Parker at ICHK Secondary. Built by Ross Parker, Sandra Kuipers and the Gibbon community (https://gibbonedu.org/about/)
Copyright © 2010, Gibbon Foundation
Gibbon™, Gibbon Education Ltd. (Hong Kong)

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

include '../../gibbon.php';

include './moduleFunctions.php';

$infoGridEntryID = $_POST['infoGridEntryID'] ?? '';
$URL = $session->get('absoluteURL').'/index.php?q=/modules/'.getModuleName($_POST['address'])."/infoGrid_manage_delete.php&infoGridEntryID=$infoGridEntryID&search=".$_GET['search'];
$URLDelete = $session->get('absoluteURL').'/index.php?q=/modules/'.getModuleName($_POST['address']).'/infoGrid_manage.php&search='.$_GET['search'];

if (isActionAccessible($guid, $connection2, '/modules/Info Grid/infoGrid_manage_delete.php') == false) {
    //Fail 0
    $URL = $URL.'&return=error0';
    header("Location: {$URL}");
} else {
    //Proceed!
    if ($infoGridEntryID == '') {
        //Fail1
        $URL = $URL.'&return=error1';
        header("Location: {$URL}");
    } else {
        try {
            $data = array('infoGridEntryID' => $infoGridEntryID);
            $sql = 'SELECT * FROM infoGridEntry WHERE infoGridEntryID=:infoGridEntryID';
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            //Fail2
            $URL = $URL.'&return=error2';
            header("Location: {$URL}");
            exit();
        }

        if ($result->rowCount() != 1) {
            //Fail 2
            $URL = $URL.'&return=error2';
            header("Location: {$URL}");
        } else {
            //Write to database
            try {
                $data = array('infoGridEntryID' => $infoGridEntryID);
                $sql = 'DELETE FROM infoGridEntry WHERE infoGridEntryID=:infoGridEntryID';
                $result = $connection2->prepare($sql);
                $result->execute($data);
            } catch (PDOException $e) {
                //Fail 2
                $URL = $URL.'&return=error2';
                header("Location: {$URL}");
                exit();
            }

            //Success 0
            $URLDelete = $URLDelete.'&return=success0';
            header("Location: {$URLDelete}");
        }
    }
}
