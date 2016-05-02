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

include '../../functions.php';
include '../../config.php';

include './moduleFunctions.php';

//New PDO DB connection
try {
    $connection2 = new PDO("mysql:host=$databaseServer;dbname=$databaseName", $databaseUsername, $databasePassword);
    $connection2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection2->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
}

@session_start();

//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]['timezone']);

$staffHandbookEntryID = $_GET['staffHandbookEntryID'];
$URL = $_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_POST['address'])."/staffHandbook_manage_edit.php&staffHandbookEntryID=$staffHandbookEntryID&search=".$_GET['search'];

if (isActionAccessible($guid, $connection2, '/modules/Staff Handbook/staffHandbook_manage_edit.php') == false) {
    //Fail 0
    $URL = $URL.'&return=error0';
    header("Location: {$URL}");
} else {
    //Proceed!
    //Check if school year specified
    if ($staffHandbookEntryID == '') {
        //Fail1
        $URL = $URL.'&return=error1';
        header("Location: {$URL}");
    } else {
        try {
            $data = array('staffHandbookEntryID' => $staffHandbookEntryID);
            $sql = 'SELECT * FROM staffHandbookEntry WHERE staffHandbookEntryID=:staffHandbookEntryID';
            $result = $connection2->prepare($sql);
            $result->execute($data);
        } catch (PDOException $e) {
            //Fail2
            $URL = $URL.'&deleteReturn=error2';
            header("Location: {$URL}");
            exit();
        }

        if ($result->rowCount() != 1) {
            //Fail 2
            $URL = $URL.'&return=error2';
            header("Location: {$URL}");
        } else {
            $row = $result->fetch();

            //Validate Inputs
            $title = $_POST['title'];
            $priority = $_POST['priority'];
            $url = $_POST['url'];
            $logoLicense = $_POST['logoLicense'];

            if ($title == '' or $priority == '' or $url == '') {
                //Fail 3
                $URL = $URL.'&return=error3';
                header("Location: {$URL}");
            } else {
                //Sort out logo
                $logo = $row['logo'];
                if ($_FILES['file']['tmp_name'] != '') {
                    $time = time();
                    //Check for folder in uploads based on today's date
                    $path = $_SESSION[$guid]['absolutePath'];
                    if (is_dir($path.'/uploads/'.date('Y', $time).'/'.date('m', $time)) == false) {
                        mkdir($path.'/uploads/'.date('Y', $time).'/'.date('m', $time), 0777, true);
                    }

                    $unique = false;
                    $count = 0;
                    while ($unique == false and $count < 100) {
                        $suffix = randomPassword(16);
                        if ($count == 0) {
                            $logo = 'uploads/'.date('Y', $time).'/'.date('m', $time)."/staffHandbook_$suffix".strrchr($_FILES['file']['name'], '.');
                        } else {
                            $logo = 'uploads/'.date('Y', $time).'/'.date('m', $time)."/staffHandbook_$suffix"."_$count".strrchr($_FILES['file']['name'], '.');
                        }

                        if (!(file_exists($path.'/'.$logo))) {
                            $unique = true;
                        }
                        ++$count;
                    }
                    if (!(move_uploaded_file($_FILES['file']['tmp_name'], $path.'/'.$logo))) {
                        $logo = '';
                    }
                }

                //Write to database
                try {
                    $data = array('title' => $title, 'priority' => $priority, 'url' => $url, 'logo' => $logo, 'logoLicense' => $logoLicense, 'staffHandbookEntryID' => $staffHandbookEntryID);
                    $sql = 'UPDATE staffHandbookEntry SET title=:title, priority=:priority, url=:url, logo=:logo, logoLicense=:logoLicense WHERE staffHandbookEntryID=:staffHandbookEntryID';
                    $result = $connection2->prepare($sql);
                    $result->execute($data);
                } catch (PDOException $e) {
                    //Fail 2
                    $URL = $URL.'&return=error2';
                    header("Location: {$URL}");
                    exit();
                }

                //Success 0
                $URL = $URL.'&return=success0';
                header("Location: {$URL}");
            }
        }
    }
}
