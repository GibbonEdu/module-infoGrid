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

use Gibbon\Http\Url;
use Gibbon\Forms\Form;

//Module includes
include './modules/Info Grid/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Info Grid/infoGrid_manage_add.php') == false) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    $page->breadcrumbs->add(__m('Manage Info Grid'), 'infoGrid_manage.php');
    $page->breadcrumbs->add(__m('Add Info Grid Entry'));

    $search = $_GET['search'] ?? '' ;

    $editLink = '';
    if (isset($_GET['editID'])) {
        $editLink = $session->get('absoluteURL').'/index.php?q=/modules/Info Grid/infoGrid_manage_edit.php&infoGridEntryID='.$_GET['editID']."&search=$search";
    }
    $page->return->setEditLink($editLink);

    if (!empty($search)) {
        $params = [
            "search" => $search
        ];
        $page->navigator->addSearchResultsAction(Url::fromModuleRoute('Info Grid', 'infoGrid_manage.php')->withQueryParams($params));
    }

    $form = Form::create('action', $session->get('absoluteURL')."/modules/Info Grid/infoGrid_manage_addProcess.php?search=$search");

    $form->addHiddenValue('address', $session->get('address'));

    $row = $form->addRow();
        $row->addLabel('title', __('Title'));
        $row->addTextField('title')->isRequired()->maxLength(100);

    $row = $form->addRow();
        $row->addLabel('staff', __('Viewable To Staff'));
        $row->addYesNo('staff')->isRequired();

    $row = $form->addRow();
        $row->addLabel('student', __('Viewable To Students'));
        $row->addYesNo('student')->isRequired();

    $row = $form->addRow();
        $row->addLabel('parent', __('Viewable To Parents'));
        $row->addYesNo('parent')->isRequired();

    $row = $form->addRow();
        $row->addLabel('priority', __('Priority'))->description(__m('Higher priorities are displayed first.'));
        $row->addNumber('priority')->maxLength(2)->setValue('0')->isRequired();

    $row = $form->addRow();
        $row->addLabel('url', __('Link'));
        $row->addURL('url')->maxLength(255)->isRequired();

    $row = $form->addRow();
        $row->addLabel('file', __('Logo'))->description(__('335px x 140px'));
        $row->addFileUpload('file')->accepts('.jpg,.jpeg,.gif,.png');

    $row = $form->addRow();
        $row->addLabel('logoLicense', __m('Logo License/Credits'));
        $row->addTextArea('logoLicense')->setRows(5);

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
}
