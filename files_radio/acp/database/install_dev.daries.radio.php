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
use wcf\system\database\table\index\DatabaseTableForeignKey;
use wcf\system\database\table\index\DatabaseTableIndex;
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
    DatabaseTable::create('radio1_stream_endpoint')
        ->columns([
            ObjectIdDatabaseTableColumn::create('endpointID'),
            NotNullInt10DatabaseTableColumn::create('streamID'),
            NotNullVarchar255DatabaseTableColumn::create('name'),
            NotNullVarchar255DatabaseTableColumn::create('path'),
            MediumtextDatabaseTableColumn::create('config'),
            DefaultFalseBooleanDatabaseTableColumn::create('isDefault'),
            NotNullInt10DatabaseTableColumn::create('showOrder')
                ->defaultValue(0),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['endpointID']),
            DatabaseTableIndex::create('name_streamID')
                ->type(DatabaseTableIndex::UNIQUE_TYPE)
                ->columns(['name', 'streamID']),
            DatabaseTableIndex::create('path_streamID')
                ->type(DatabaseTableIndex::UNIQUE_TYPE)
                ->columns(['path', 'streamID']),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['streamID'])
                ->referencedTable('radio1_stream')
                ->referencedColumns(['streamID'])
                ->onDelete('CASCADE'),
        ]),
];
