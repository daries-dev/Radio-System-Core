<?php

namespace radio\data\stream\endpoint;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;

/**
 * Executes stream-endpoint-related actions.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 *
 * @method  StreamEndpointEditor[]  getObjects()
 * @method  StreamEndpointEditor    getSingleObject()
 */
class StreamEndpointAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    public $className = StreamEndpointEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.radio.stream.canAddEndpoint'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.radio.stream.canDeleteEndpoint'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.radio.stream.canEditEndpoint'];

    /**
     * @inheritDoc
     */
    public function create(): StreamEndpoint
    {
        if (isset($this->parameters['data']['showOrder']) && $this->parameters['data']['showOrder'] !== null) {
            $sql = "UPDATE  radio1_stream_endpoint
                    SET     showOrder = showOrder + 1
                    WHERE   showOrder >= ?
                    AND     streamID = ?";
            $statment = WCF::getDB()->prepare($sql);
            $statment->execute([
                $this->parameters['data']['showOrder'],
                $this->parameters['data']['streamID'],
            ]);
        }

        return parent::create();
    }

    public function delete(): int
    {
        $returnValues = parent::delete();

        $sql = "UPDATE  radio1_stream_endpoint
                SET     showOrder = showOrder - 1
                WHERE   showOrder > ?
                AND     streamID = ?";
        $statement = WCF::getDB()->prepare($sql);
        foreach ($this->getObjects() as $object) {
            $statement->execute([
                $object->showOrder,
                $object->streamID,
            ]);
        }

        return $returnValues;
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        parent::update();

        foreach ($this->getObjects() as $object) {
            // update show order
            if (isset($this->parameters['data']['showOrder']) && $this->parameters['data']['showOrder'] !== null) {
                $sql = "UPDATE  radio1_stream_endpoint
                        SET     showOrder = showOrder + 1
                        WHERE   showOrder >= ?
                        AND     endpointID <> ?
                        AND     streamID = ?";
                $statment = WCF::getDB()->prepare($sql);
                $statment->execute([
                    $this->parameters['data']['showOrder'],
                    $object->endpointID,
                    $object->streamID,
                ]);

                $sql = "UPDATE  radio1_stream_endpoint
                        SET     showOrder = showOrder -1
                        WHERE   showOrder > ?
                        AND     streamID = ?";
                $statment = WCF::getDB()->prepare($sql);
                $statment->execute([
                    $object->showOrder,
                    $object->streamID,
                ]);
            }
        }
    }
}
