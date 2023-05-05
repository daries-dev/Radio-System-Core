<?php

namespace radio\acp\form;

use radio\data\stream\StreamAction;
use radio\data\stream\StreamList;
use radio\system\stream\option\IStreamOption;
use wcf\data\object\type\ObjectTypeCache;
use wcf\form\AbstractForm;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\ImplementationException;
use wcf\system\exception\NamedUserException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\TabFormContainer;
use wcf\system\form\builder\container\TabMenuFormContainer;
use wcf\system\form\builder\container\TabTabMenuFormContainer;
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
use wcf\util\StringUtil;

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
     * object type id
     */
    public int $objectTypeID = 0;

    /**
     * stream option obj
     */
    public ?IStreamOption $optionObj = null;

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

        $optionsTab = TabTabMenuFormContainer::create('optionsTab');
        $optionsTab->label('wcf.form.field.option');

        $tabMenu->appendChildren([
            $dataTab,
            $optionsTab,
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

        // adding options
        $this->getOptionObject()->addOptions($optionsTab);
    }

    /**
     * @inheritDoc
     */
    protected function finalizeForm()
    {
        $this->form->getDataHandler()->addProcessor(
            new CustomFormDataProcessor(
                'objectTypeID',
                function (IFormDocument $document, array $parameters) {
                    $parameters['data']['objectTypeID'] = $this->objectTypeID;

                    return $parameters;
                }
            )
        );

        $this->getOptionObject()->addProcessors($this->form->getDataHandler());
    }

    protected function getOptionObject(): IStreamOption
    {
        if ($this->optionObj === null) {
            $objectType = ObjectTypeCache::getInstance()->getObjectType($this->objectTypeID);
            $typeName = StringUtil::firstCharToUpperCase($objectType->getProcessor()->getStreamTypeName());

            $className = 'radio\system\stream\option\\' . $typeName . 'StreamOption';
            if (!\class_exists($className)) {
                throw new \LogicException("Unable to find class '" . $className . "'.");
            }

            if (!\is_subclass_of($className, IStreamOption::class)) {
                throw new ImplementationException($className, IStreamOption::class);
            }

            $this->optionObj = new $className();
        }

        return $this->optionObj;
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
        $objectTypes = ObjectTypeCache::getInstance()->getObjectTypes('dev.daries.radio.stream.type');

        if (!\count($objectTypes)) {
            throw new NamedUserException(WCF::getLanguage()->get('radio.acp.stream.error.noStreamTypes'));
        }

        if (!empty($_REQUEST['objectTypeID'])) {
            $this->objectTypeID = \intval($_REQUEST['objectTypeID']);
        }

        // work-around to force adding stream via dialog overlay
        if (\count($objectTypes) > 1 && empty($_POST) && !isset($_REQUEST['objectTypeID'])) {
            $parameters = [
                'application' => 'radio',
                'showStreamAddDialog' => 1,
            ];
            HeaderUtil::redirect(LinkHandler::getInstance()->getLink('StreamList', $parameters));
            exit;
        } else if (\count($objectTypes) == 1 && empty($_POST) && !isset($_REQUEST['objectTypeID'])) {
            $firstObjectType = \reset($objectTypes);
            $this->objectTypeID = $firstObjectType->objectTypeID;
        }
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        AbstractForm::save();

        $action = $this->formAction;
        if ($this->objectActionName) {
            $action = $this->objectActionName;
        } elseif ($this->formAction === 'edit') {
            $action = 'update';
        }

        $formData = $this->form->getData();
        if (!isset($formData['data'])) {
            $formData['data'] = [];
        }
        $formData['data'] = \array_merge($this->additionalFields, $formData['data']);

        if (isset($formData['data']['config'])) {
            $formData['data']['config'] = \serialize($formData['data']['config']);
        }

        /** @var AbstractDatabaseObjectAction objectAction */
        $this->objectAction = new $this->objectActionClass(
            \array_filter([$this->formObject]),
            $action,
            $formData
        );
        $this->objectAction->executeAction();

        $this->saved();

        WCF::getTPL()->assign('success', true);

        if ($this->formAction === 'create' && $this->objectEditLinkController) {
            WCF::getTPL()->assign(
                'objectEditLink',
                LinkHandler::getInstance()->getControllerLink($this->objectEditLinkController, [
                    'application' => $this->objectEditLinkApplication,
                    'id' => $this->objectAction->getReturnValues()['returnValues']->getObjectID(),
                ])
            );
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
            $parameters['objectTypeID'] = $this->objectTypeID;
        }

        $this->form->action(LinkHandler::getInstance()->getControllerLink(static::class, $parameters));
    }
}
