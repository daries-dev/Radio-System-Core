<?php

namespace radio\data\stream;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\package\PackageCache;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;

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

    /**
     * @inheritDoc
     */
    public function create(): Stream
    {
        if (isset($this->parameters['data']['showOrder']) && $this->parameters['data']['showOrder'] !== null) {
            $sql = "UPDATE  radio1_stream
                    SET     showOrder = showOrder + 1
                    WHERE   showOrder >= ?";
            $statment = WCF::getDB()->prepare($sql);
            $statment->execute([
                $this->parameters['data']['showOrder'],
            ]);
        }

        // The streamname cannot be empty by design, but cannot be filled proper if the
        // multilingualism is enabled, therefore, we must fill the streamname with a dummy value.
        if (!isset($this->parameters['data']['streamname']) && isset($this->parameters['streamname_i18n'])) {
            $this->parameters['data']['streamname'] = 'radio.stream.streamname';
        }

        /** @var Stream $stream */
        $stream = parent::create();

        // i18n
        $updateData = [];
        if (isset($this->parameters['streamname_i18n'])) {
            I18nHandler::getInstance()->save(
                $this->parameters['streamname_i18n'],
                'radio.stream.streamname' . $stream->streamID,
                'radio.stream',
                PackageCache::getInstance()->getPackageID('dev.daries.radio')
            );

            $updateData['streamname'] = 'radio.stream.streamname' . $stream->streamID;
        }

        if (!empty($updateData)) {
            $streamEditor = new StreamEditor($stream);
            $streamEditor->update($updateData);
        }

        return $stream;
    }

    public function delete(): int
    {
        $returnValues = parent::delete();

        $sql = "UPDATE  radio1_stream
                SET     showOrder = showOrder - 1
                WHERE   showOrder > ?";
        $statement = WCF::getDB()->prepare($sql);
        foreach ($this->getObjects() as $object) {
            $statement->execute([
                $object->showOrder
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
            $updateData = [];

            //i18n
            if (isset($this->parameters['streamname_i18n'])) {
                I18nHandler::getInstance()->save(
                    $this->parameters['streamname_i18n'],
                    'radio.stream.streamname' . $object->streamID,
                    'radio.stream',
                    PackageCache::getInstance()->getPackageID('dev.daries.radio')
                );

                $updateData['streamname'] = 'radio.stream.streamname' . $object->streamID;
            }

            // update show order
            if (isset($this->parameters['data']['showOrder']) && $this->parameters['data']['showOrder'] !== null) {
                $sql = "UPDATE  radio1_stream
                        SET     showOrder = showOrder + 1
                        WHERE   showOrder >= ?
                        AND     streamID <> ?";
                $statment = WCF::getDB()->prepare($sql);
                $statment->execute([
                    $this->parameters['data']['showOrder'],
                    $object->streamID,
                ]);

                $sql = "UPDATE  radio1_stream
                        SET     showOrder = showOrder -1
                        WHERE   showOrder > ?";
                $statment = WCF::getDB()->prepare($sql);
                $statment->execute([
                    $object->showOrder,
                ]);
            }

            if (!empty($updateData)) {
                $object->update($updateData);
            }
        }
    }
}
