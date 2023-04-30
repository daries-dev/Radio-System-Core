<?php

namespace radio\acp\form;

use radio\data\stream\StreamAction;
use radio\data\stream\StreamList;
use radio\system\stream\type\StreamTypeHandler;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\NamedUserException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\TabFormContainer;
use wcf\system\form\builder\container\TabMenuFormContainer;
use wcf\system\form\builder\data\processor\CustomFormDataProcessor;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\builder\field\ShowOrderFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\IFormDocument;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Shows the stream add form.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 */
class StreamAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'radio.acp.menu.link.stream.add';

    /**
     * @inheritDoc
     */
    public $objectEditLinkController = StreamEditForm::class;

    /**
     * @inheritDoc
     */
    public $objectEditLinkApplication = 'radio';
    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.radio.stream.canAddStream'];

    /**
     * @inheritDoc
     */
    public $objectActionClass = StreamAction::class;

    /**
     * stream type id
     */
    public int $streamTypeID = 0;

    /**
     * @inheritDoc
     */
    protected function createForm(): void
    {
        parent::createForm();

        $tabMenu = TabMenuFormContainer::create('stream');
        $this->form->appendChild($tabMenu);

        // create tabs
        $dataTab = TabFormContainer::create('dataTab');
        $dataTab->label('wcf.global.form.data');

        $tabMenu->appendChildren([
            $dataTab,
        ]);

        // create data container
        $dataContainer = FormContainer::create('dataContainer')
            ->appendChildren([
                TextFormField::create('streamname')
                    ->label('radio.stream.streamname')
                    ->maximumLength(255)
                    ->required()
                    ->i18n()
                    ->languageItemPattern('radio.stream\d+'),
                TextFormField::create('host')
                    ->label('radio.stream.host')
                    ->maximumLength(255)
                    ->required()
                    ->immutable()
                    ->value('localhost'),
                IntegerFormField::create('port')
                    ->label('radio.stream.port')
                    ->minimum(1000)
                    ->maximum(65535)
                    ->required()
                    ->value(8000)
                    ->addValidator(new FormFieldValidator('use', function (IntegerFormField $formField) {
                        if (
                            $this->formObject === null ||
                            $this->formObject->port !== $formField->getSaveValue()
                        ) {
                            $streamList = new StreamList();
                            $streamList->getConditionBuilder()->add('port = ?', [$formField->getSaveValue()]);

                            if ($streamList->countObjects()) {
                                $formField->addValidationError(
                                    new FormFieldValidationError(
                                        'use',
                                        'radio.acp.stream.port.error.use'
                                    )
                                );
                            }
                        }
                    })),
                ShowOrderFormField::create()
                    ->description('radio.acp.stream.showOrder.description')
                    ->options(new StreamList()),
            ]);
        $dataTab->appendChild($dataContainer);

        $this->form->getDataHandler()->addProcessor(
            new CustomFormDataProcessor(
                'streamTypeID',
                function (IFormDocument $document, array $parameters) {
                    $parameters['data']['streamTypeID'] = $this->streamTypeID;

                    return $parameters;
                }
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        $this->readStreamType();
    }

    /**
     * Reads the stream types for this stream form.
     */
    protected function readStreamType(): void
    {
        if (!\count(StreamTypeHandler::getInstance()->getStreamTypes())) {
            throw new NamedUserException(WCF::getLanguage()->get('radio.acp.stream.error.noStreamTypes'));
        }

        if (!empty($_REQUEST['streamTypeID'])) {
            $this->streamTypeID = \intval($_REQUEST['streamTypeID']);
        }

        // work-around to force adding stream via dialog overlay
        if (\count(StreamTypeHandler::getInstance()->getStreamTypes()) > 1 && empty($_POST) && !isset($_REQUEST['streamTypeID'])) {
            $parameters = [
                'application' => 'radio',
                'showStreamAddDialog' => 1,
            ];
            HeaderUtil::redirect(LinkHandler::getInstance()->getLink('StreamList', $parameters));
            exit;
        } else if (\count(StreamTypeHandler::getInstance()->getStreamTypes()) == 1 && empty($_POST) && !isset($_REQUEST['streamTypeID'])) {
            $streamTypes = StreamTypeHandler::getInstance()->getStreamTypes();
            $firstStreamType = $streamTypes[0];
            $this->streamTypeID = $firstStreamType->getStreamTypeID();
        }
    }

    /**
     * @inheritDoc
     */
    protected function setFormAction(): void
    {
        $parameters = [];
        if ($this->formObject !== null) {
            if ($this->formObject instanceof IRouteController) {
                $parameters['object'] = $this->formObject;
            } else {
                $object = $this->formObject;

                $parameters['id'] = $object->{$object::getDatabaseTableIndexName()};
            }
        } else {
            $parameters['streamTypeID'] = $this->streamTypeID;
        }

        $this->form->action(LinkHandler::getInstance()->getControllerLink(static::class, $parameters));
    }
}
