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

//This file describes the module, including database tables

//Basic variables
$name = 'Staff Handbook';
$description = 'Offers a school-defined image-grid set of links to useful resources, for staff.';
$entryURL = 'staffHandbook_view.php';
$type = 'Additional';
$category = 'Other';
$version = '1.0.05';
$author = 'Ross Parker';
$url = 'http://rossparker.org';

//Module tables
$moduleTables[0] = "CREATE TABLE `staffHandbookEntry` (
  `staffHandbookEntryID` int(8) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `priority` INT(2) NOT NULL DEFAULT '0',
  `url` text NOT NULL,
  `logo` varchar(255) NULL,
  `logoLicense` text NOT NULL,
  `gibbonPersonIDCreator` int(8) unsigned zerofill NOT NULL,
  `timestampCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`staffHandbookEntryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;";

//Action rows
$actionRows[0]['name'] = 'Manage Staff Handbook';
$actionRows[0]['precedence'] = '0';
$actionRows[0]['category'] = 'Staff Handbook';
$actionRows[0]['description'] = 'Allows a user to define and edit entires in the Staff Handbook.';
$actionRows[0]['URLList'] = 'staffHandbook_manage.php, staffHandbook_manage_add.php, staffHandbook_manage_edit.php, staffHandbook_manage_delete.php';
$actionRows[0]['entryURL'] = 'staffHandbook_manage.php';
$actionRows[0]['defaultPermissionAdmin'] = 'Y';
$actionRows[0]['defaultPermissionTeacher'] = 'N';
$actionRows[0]['defaultPermissionStudent'] = 'N';
$actionRows[0]['defaultPermissionParent'] = 'N';
$actionRows[0]['defaultPermissionSupport'] = 'N';
$actionRows[0]['categoryPermissionStaff'] = 'Y';
$actionRows[0]['categoryPermissionStudent'] = 'N';
$actionRows[0]['categoryPermissionParent'] = 'N';
$actionRows[0]['categoryPermissionOther'] = 'N';

$actionRows[1]['name'] = 'View Staff Handbook';
$actionRows[1]['precedence'] = '0';
$actionRows[1]['category'] = 'Staff Handbook';
$actionRows[1]['description'] = 'Allows a user to view the Staff Handbook.';
$actionRows[1]['URLList'] = 'staffHandbook_view.php';
$actionRows[1]['entryURL'] = 'staffHandbook_view.php';
$actionRows[1]['defaultPermissionAdmin'] = 'Y';
$actionRows[1]['defaultPermissionTeacher'] = 'Y';
$actionRows[1]['defaultPermissionStudent'] = 'N';
$actionRows[1]['defaultPermissionParent'] = 'N';
$actionRows[1]['defaultPermissionSupport'] = 'N';
$actionRows[1]['categoryPermissionStaff'] = 'Y';
$actionRows[1]['categoryPermissionStudent'] = 'N';
$actionRows[1]['categoryPermissionParent'] = 'N';
$actionRows[1]['categoryPermissionOther'] = 'N';

$actionRows[2]['name'] = 'Credits & Licenses';
$actionRows[2]['precedence'] = '1';
$actionRows[2]['category'] = 'Staff Handbook';
$actionRows[2]['description'] = 'Allows a user to view image credits for logo images.';
$actionRows[2]['URLList'] = 'staffHandbook_credits.php';
$actionRows[2]['entryURL'] = 'staffHandbook_credits.php';
$actionRows[2]['defaultPermissionAdmin'] = 'Y';
$actionRows[2]['defaultPermissionTeacher'] = 'Y';
$actionRows[2]['defaultPermissionStudent'] = 'Y';
$actionRows[2]['defaultPermissionParent'] = 'Y';
$actionRows[2]['defaultPermissionSupport'] = 'Y';
$actionRows[2]['categoryPermissionStaff'] = 'Y';
$actionRows[2]['categoryPermissionStudent'] = 'Y';
$actionRows[2]['categoryPermissionParent'] = 'Y';
$actionRows[2]['categoryPermissionOther'] = 'Y';

//HOOKS
$array = array();
$array['sourceModuleName'] = 'Staff Handbook';
$array['sourceModuleAction'] = 'View Staff Handbook';
$array['sourceModuleInclude'] = 'hook_staffDashboard_staffHandbookView.php';
$hooks[0] = "INSERT INTO `gibbonHook` (`gibbonHookID`, `name`, `type`, `options`, gibbonModuleID) VALUES (NULL, 'Staff Handbook', 'Staff Dashboard', '".serialize($array)."', (SELECT gibbonModuleID FROM gibbonModule WHERE name='$name'));";
