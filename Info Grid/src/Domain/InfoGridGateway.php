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
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

namespace Gibbon\Module\InfoGrid\Domain;

use Gibbon\Domain\Traits\TableAware;
use Gibbon\Domain\QueryCriteria;
use Gibbon\Domain\QueryableGateway;

/**
 * InfoGridGateway Gateway
 *
 * @version v18
 * @since   v18
 */
class InfoGridGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'infoGridEntry';

    private static $searchableColumns = ['i.title'];

    public function queryInfoGrid(QueryCriteria $criteria)
    {
        $query = $this->getDefaultTableQuery();

        $criteria->addFilterRules($this->getSharedQueryFilters());

        return $this->runQuery($query, $criteria);
    }

    public function queryInfoGridCreator(QueryCriteria $criteria)
    {
        $query = $this->getDefaultTableQuery()
            ->innerJoin('gibbonPerson p on p.gibbonPersonID = i.gibbonPersonIDCreator');
        
        $query->cols([
            'p.title as creatorTitle',
            'p.surname as creatorSurname',
            'p.firstname as creatorFirstname',
            'p.preferredName as creatorPreferredName',
            'p.officialName as creatorOfficialName'
        ]);
        
        $criteria->addFilterRules($this->getSharedQueryFilters());
        
        return $this->runQuery($query, $criteria);
    }

    private function getSharedQueryFilters()
    {
        return [
            'isStaff' => function ($query, $needle) {
                return $query->where("i.staff = :staffEnum")
                    ->bindValue('staffEnum', $needle);
            },
            'isStudent' => function ($query, $needle) {
                return $query->where("i.student = :studentEnum")
                    ->bindValue('studentEnum', $needle);
            },
            'isParent' => function ($query, $needle) {
                return $query->where("i.staff = :parentEnum")
                    ->bindValue('parentEnum', $needle);
            },
            'creator' => function ($query, $needle) {
                return $query->where("i.gibbonPersonIDCreator = :creatorID")
                    ->bindValue('creatorID', $needle);
            },
        ];
    }
    
    private function getDefaultTableQuery()
    {
        return $this
            ->newQuery()
            ->from($this->getTableName(). ' as i')
            ->cols([
                'i.infoGridEntryID as infoGridEntryID',
                'i.title as title',
                'i.staff as staff',
                'i.student as student',
                'i.parent as parent',
                'i.priority as priority',
                'i.url as url',
                'i.logo as logo',
                'i.logoLicense as logoLicense',
                'i.gibbonPersonIDCreator as gibbonPersonIDCreator',
                'i.timestampCreated as timestampCreated'
            ]);
    }
}
