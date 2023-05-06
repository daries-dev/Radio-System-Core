<?php

/**
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 */

use wcf\system\database\table\column\DefaultFalseBooleanDatabaseTableColumn;
use wcf\system\database\table\column\MediumtextDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar255DatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;

return [
    DatabaseTable::create('radio1_stream')
        ->columns([
            ObjectIdDatabaseTableColumn::create('streamID'),
            NotNullVarchar255DatabaseTableColumn::create('streamname')
                ->defaultValue(''),
            NotNullVarchar255DatabaseTableColumn::create('host')
                ->defaultValue(''),
            NotNullInt10DatabaseTableColumn::create('port')
                ->defaultValue(0),
            MediumtextDatabaseTableColumn::create('config'),
            NotNullInt10DatabaseTableColumn::create('showOrder')
                ->defaultValue(0),
            DefaultFalseBooleanDatabaseTableColumn::create('isDisabled'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['streamID']),
        ]),
];
