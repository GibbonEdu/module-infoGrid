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

use Gibbon\Module\InfoGrid\Tables\InfoGrid;

$returnInt = null;

if (isActionAccessible($guid, $connection2, '/modules/Info Grid/infoGrid_view.php') == false) {
    //Acess denied
    $returnInt .= "<div class='error'>";
    $returnInt .= 'You do not have access to this action.';
    $returnInt .= '</div>';
} else {
    // Add the module manually to autoloader because it's hooked from the dashboard
    global $container, $autoloader;
    $autoloader->addPsr4('Gibbon\\Module\\InfoGrid\\', realpath(__DIR__).'/src');

    $roleCategory = getRoleCategory($session->get('gibbonRoleIDCurrent'), $connection2);
    $canManage = isActionAccessible($guid, $connection2, '/modules/Info Grid/infoGrid_manage.php');

    $table = $container->get(InfoGrid::class)->create($roleCategory, $canManage);
    $returnInt .= $table->getOutput();
}

return $returnInt;
