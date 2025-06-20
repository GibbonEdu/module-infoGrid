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

use Gibbon\Forms\Form;
use Gibbon\Tables\DataTable;
use Gibbon\Services\Format;
use Gibbon\Module\InfoGrid\Domain\InfoGridGateway;

//Module includes
include './modules/Info Grid/moduleFunctions.php';


if (isActionAccessible($guid, $connection2, '/modules/Info Grid/infoGrid_manage.php') == false) {
    //Acess denied
    $page->addError(__('You do not have access to this action.'));
} else {
    $page->breadcrumbs->add(__m('Manage Info Grid'));

    $search = isset($_GET['search'])? $_GET['search'] : '';

    $form = Form::create('search', $session->get('absoluteURL').'/index.php', 'get');
    $form->setTitle(__('Search'));
    $form->setClass('noIntBorder w-full');

    $form->addHiddenValue('q', '/modules/'.$session->get('module').'/infoGrid_manage.php');

    $row = $form->addRow();
        $row->addLabel('search', __('Search For'))->description(__('Title'));
        $row->addTextField('search')->setValue($search);

    $row = $form->addRow();
        $row->addSearchSubmit($session, __('Clear Search'));

    echo $form->getOutput();

    $igGateway = $container->get(InfoGridGateway::class);
    $criteria = $igGateway->newQueryCriteria()
        ->searchBy('i.title', $_GET['search'] ?? '')
        ->fromPost();
        
    $igrid = $igGateway->queryInfoGrid($criteria);

    $table = DataTable::createPaginated('infogrid', $criteria);
    $table->setTitle(__('View'));

    $table
        ->addHeaderAction('add', __('Add'))
        ->setURL('/modules/Info Grid/infoGrid_manage_add.php')
        ->addParam('search', $_GET['search'] ?? '')
        ->displayLabel();

    $table->addColumn('logo', __('Logo'))
        ->width('100px')
        ->format(Format::using('userPhoto', [
            'logo',
            75
        ]));

    $table->addColumn('title', __('Name'))->format(Format::using('link', ['url','title']));
    $table->addColumn('staff', __('Staff'))->format(Format::using('yesNo', ['staff']));
    $table->addColumn('student', __('Student'))->format(Format::using('yesNo', ['student']));
    $table->addColumn('parent', __('Parent'))->format(Format::using('yesNo', ['parent']));
    $table->addColumn('priority', __('Priority'));

    $actions = $table->addActionColumn()
        ->addParam('infoGridEntryID')
        ->addParam('search', $_GET['search'] ?? '')
        ->format(function ($infoGridItem, $actions) {
            $actions
                ->addAction('edit', 'Edit')
                ->setURL('/modules/Info Grid/infoGrid_manage_edit.php');

            $actions
                ->addAction('delete', 'Delete')
                ->setURL('/modules/Info Grid/infoGrid_manage_delete.php');
        });

    echo $table->render($igrid);
}
