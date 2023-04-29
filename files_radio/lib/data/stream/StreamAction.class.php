<?php

namespace radio\data\stream;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes stream-related actions.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 *
 * @method  StreamEditor[]  getObjects()
 * @method  StreamEditor    getSingleObject()
 */
class StreamAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    public $className = StreamEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.radio.stream.canAddStream'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.radio.stream.canDeleteStream'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.radio.stream.canEditStream'];
}
