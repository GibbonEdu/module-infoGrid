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
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

namespace Gibbon\Module\InfoGrid\Tables;

use Gibbon\Services\Format;
use Gibbon\Tables\DataTable;
use Gibbon\Tables\View\GridView;
use Gibbon\Module\InfoGrid\Domain\InfoGridGateway;

/**
 * InfoGrid
 */
class InfoGrid
{
    protected $infoGridGateway;
    protected $gridRenderer;

    public function __construct(InfoGridGateway $infoGridGateway, GridView $gridRenderer)
    {
        $this->infoGridGateway = $infoGridGateway;
        $this->gridRenderer = $gridRenderer;
    }

    public function create($roleCategory, $canManage = false) {
        $criteria = $this->infoGridGateway->newQueryCriteria()
            ->filterBy('is'.$roleCategory, 'Y')
            ->searchBy('title')
            ->sortBy('priority', 'DESC')
            ->sortBy('title')
            ->fromPost();
            
        $info = $this->infoGridGateway->queryInfoGrid($criteria);

        $table = DataTable::create('infoGrid')->setRenderer($this->gridRenderer)->withData($info);

        $table->addHeaderAction('credits', __m('Credits & Licensing'))
                ->setURL('/modules/Info Grid/infoGrid_credits.php')
                ->displayLabel();

        if ($canManage) {
            $table->addHeaderAction('edit', __('Edit'))
                ->setURL('/modules/Info Grid/infoGrid_manage.php')
                ->displayLabel();
        }

        $table->addMetaData('gridClass', 'flex items-stretch border rounded bg-blue-50');
        $table->addMetaData('gridItemClass', 'w-full sm:w-1/2 p-4 text-center text-sm leading-normal');

        $table->addColumn('logo')
            ->format(function ($info) {
                $logo = !empty($info['logo'])
                    ? $info['logo']
                    : 'modules/Info Grid/img/anonymous.jpg';
                return Format::link($info['url'], Format::photo(trim($logo,'/'), 140, 'w-full p-1'));
            });

        $table->addColumn('link')
            ->format(function ($info) {
                return Format::link($info['url'], $info['title']);
            });

        return $table;
    }
}
