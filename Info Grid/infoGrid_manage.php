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

use Gibbon\Forms\Form;
use Gibbon\Tables\DataTable;
use Gibbon\Services\Format;
use Gibbon\Domain\InfoGrid\InfoGridGateway;

//Module includes
include './modules/Info Grid/moduleFunctions.php';


if (isActionAccessible($guid, $connection2, '/modules/Info Grid/infoGrid_manage.php') == false) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    $page->breadcrumbs->add(__('Manage Info Grid'));

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    //Set pagination variable
    $currentPage = null;
    if (isset($_GET['page'])) {
        $currentPage = $_GET['page'];
    }
    if ((!is_numeric($page)) or $page < 1) {
        $currentPage = 1;
    }

    $search = isset($_GET['search'])? $_GET['search'] : '';

    echo "<h2 class='top'>";
    echo __('Search');
    echo '</h2>';

    $form = Form::create('search', $_SESSION[$guid]['absoluteURL'].'/index.php', 'get');
    $form->setClass('noIntBorder fullWidth');

    $form->addHiddenValue('q', '/modules/'.$_SESSION[$guid]['module'].'/infoGrid_manage.php');

    $row = $form->addRow();
        $row->addLabel('search', __('Search For'))->description(__('Title'));
        $row->addTextField('search')->setValue($search);

    $row = $form->addRow();
        $row->addSearchSubmit($gibbon->session, __('Clear Search'));

    echo $form->getOutput();

    echo "<h2 class='top'>";
    echo __('View');
    echo '</h2>';

    try {
        $data = array();
        $sql = 'SELECT infoGridEntry.* FROM infoGridEntry ORDER BY priority DESC, title';
        if ($search != '') {
            $data = array('search1' => "%$search%");
            $sql = 'SELECT infoGridEntry.* FROM infoGridEntry WHERE infoGridEntry.title LIKE :search1 ORDER BY priority DESC, title';
        }
        $sqlPage = $sql.' LIMIT '.$_SESSION[$guid]['pagination'].' OFFSET '.(($currentPage - 1) * $_SESSION[$guid]['pagination']);
        $result = $connection2->prepare($sql);
        $result->execute($data);
    } catch (PDOException $e) {
        $page->addError($e->getMessage());
    }

    echo "<div class='linkTop'>";
    echo "<a href='".$_SESSION[$guid]['absoluteURL']."/index.php?q=/modules/Info Grid/infoGrid_manage_add.php&search=$search'>".__('Add')."<img style='margin-left: 5px' title='".__('Add')."' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/page_new.png'/></a>";
    echo '</div>';

    if ($result->rowCount() < 1) {
        $page->addError(__('There are no records to display.'));
    } else {
        if ($result->rowCount() > $_SESSION[$guid]['pagination']) {
            printPagination($guid, $result->rowCount(), $currentPage, $_SESSION[$guid]['pagination'], 'top', "search=$search");
        }

        echo "<table cellspacing='0' style='width: 100%'>";
        echo "<tr class='head'>";
        echo "<th style='width: 180px'>";
        echo __('Logo');
        echo '</th>';
        echo '<th>';
        echo 'Name<br/>';
        echo '</th>';
        echo '<th>';
        echo 'Staff<br/>';
        echo '</th>';
        echo '<th>';
        echo 'Student<br/>';
        echo '</th>';
        echo '<th>';
        echo 'Parent<br/>';
        echo '</th>';
        echo '<th>';
        echo 'Priority<br/>';
        echo '</th>';
        echo "<th style='width: 120px'>";
        echo 'Actions';
        echo '</th>';
        echo '</tr>';

        $count = 0;
        $rowNum = 'odd';
        try {
            $resultPage = $connection2->prepare($sqlPage);
            $resultPage->execute($data);
        } catch (PDOException $e) {
            $page->addError($e->getMessage());
        }
        while ($row = $resultPage->fetch()) {
            if ($count % 2 == 0) {
                $rowNum = 'even';
            } else {
                $rowNum = 'odd';
            }
            ++$count;

			//COLOR ROW BY STATUS!
			echo "<tr class=$rowNum>";
            echo '<td>';
            if ($row['logo'] != '') {
                echo "<img class='user' style='width: 168px; height: 70px' src='".$_SESSION[$guid]['absoluteURL'].'/'.$row['logo']."'/>";
            } else {
                echo "<img class='user' style='width: 168px; height: 70px' src='".$_SESSION[$guid]['absoluteURL']."/modules/Info Grid/img/anonymous.jpg'/>";
            }
            echo '</td>';
            echo '<td>';
            echo "<a href='".$row['url']."'>".$row['title'].'</a>';
            echo '</td>';
            echo '<td>';
            echo ynExpander($guid, $row['staff']);
            echo '</td>';
            echo '<td>';
            echo ynExpander($guid, $row['student']);
            echo '</td>';
            echo '<td>';
            echo ynExpander($guid, $row['parent']);
            echo '</td>';
            echo '<td>';
            echo $row['priority'];
            echo '</td>';
            echo '<td>';
            echo "<a href='".$_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Info Grid/infoGrid_manage_edit.php&infoGridEntryID='.$row['infoGridEntryID']."&search=$search'><img title='Edit' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/config.png'/></a> ";
            echo "<a class='thickbox' href='".$_SESSION[$guid]['absoluteURL'].'/fullscreen.php?q=/modules/Info Grid/infoGrid_manage_delete.php&infoGridEntryID='.$row['infoGridEntryID']."&search=$search&width=650&height=135'><img title='Delete' src='./themes/".$_SESSION[$guid]['gibbonThemeName']."/img/garbage.png'/></a> ";
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';

        if ($result->rowCount() > $_SESSION[$guid]['pagination']) {
            printPagination($guid, $result->rowCount(), $currentPage, $_SESSION[$guid]['pagination'], 'bottom', "search=$search");
        }
    }

    //$igGateway = $container->get(InfoGridGateway::class);
    //$table = DataTable::createPaginated('infogrid',$criteria);
}
