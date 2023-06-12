<?php

namespace radio\acp\form;

use radio\data\stream\endpoint\StreamEndpoint;
use radio\data\stream\endpoint\StreamEndpointAction;
use radio\data\stream\endpoint\StreamEndpointCache;
use radio\data\stream\endpoint\StreamEndpointList;
use radio\data\stream\Stream;
use radio\system\form\builder\data\processor\StreamOptionFormDataProcessor;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\TabFormContainer;
use wcf\system\form\builder\container\TabMenuFormContainer;
use wcf\system\form\builder\data\processor\CustomFormDataProcessor;
use wcf\system\form\builder\data\processor\DefaultFormDataProcessor;
use wcf\system\form\builder\field\dependency\ValueFormFieldDependency;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\builder\field\PasswordFormField;
use wcf\system\form\builder\field\ShowOrderFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\UrlFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\IFormDocument;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\FileUtil;

/**
 * Shows the stream endpoint add form.
 * 
 * @author	Marco Daries
 * @copyright	2023 Daries.dev
 * @license	Attribution-NoDerivatives 4.0 International (CC BY-ND 4.0) <https://creativecommons.org/licenses/by-nd/4.0/>
 */
class StreamEndpointAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'radio.acp.menu.link.stream.list';

    /**
     * available options
     */
    protected array $availableOptions = [
        'bitrate' => true,
        'encoder' => true,
        'mp3Mode' => true,
        'mp3Quality' => true,
        'sampleRate' => true,
        'streamAdminPassword' => true,
        'streamMaxUser ' => true,
        'streamPassword ' => true,
        'streamRelayURL' => true,
    ];

    /**
     * @inheritDoc
     */
    public $objectEditLinkController = StreamEndpointEditForm::class;

    /**
     * @inheritDoc
     */
    public $objectEditLinkApplication = 'radio';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.radio.stream.canAddEndpoint'];

    /**
     * @inheritDoc
     */
    public $objectActionClass = StreamEndpointAction::class;

    /**
     * stream object
     */
    public Stream $stream;

    /**
     * stream id
     */
    public int $streamID = 0;

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'stream' => $this->stream,
            'streamID' => $this->streamID,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function createForm(): void
    {
        parent::createForm();

        $tabMenu = TabMenuFormContainer::create('endpoint');
        $this->form->appendChild($tabMenu);

        // create tabs
        $dataTab = TabFormContainer::create('dataTab');
        $dataTab->label('wcf.global.form.data');

        $optionsTab = TabFormContainer::create('optionsTab');
        $optionsTab->label('wcf.form.field.option');

        $tabMenu->appendChildren([
            $dataTab,
            $optionsTab,
        ]);

        // create data container
        $dataContainer = FormContainer::create('dataContainer')
            ->appendChildren([
                TextFormField::create('name')
                    ->label('wcf.global.name')
                    ->required()
                    ->maximumLength(255)
                    ->addValidator(new FormFieldValidator('uniqueness', function (TextFormField $formField) {
                        $formField->value(
                            \mb_convert_case($formField->getSaveValue(), \MB_CASE_LOWER)
                        );

                        if (
                            $this->formObject === null ||
                            $this->formObject->name !== $formField->getSaveValue()
                        ) {
                            $list = new StreamEndpointList();
                            $list->getConditionBuilder()->add('name = ?', [$formField->getSaveValue()]);
                            $list->getConditionBuilder()->add('streamID = ?', [$this->stream->streamID]);
                            if ($list->countObjects() > 0) {
                                $formField->addValidationError(
                                    new FormFieldValidationError(
                                        'notUnique',
                                        'radio.acp.stream.option.endpoint.name.error.notUnique'
                                    )
                                );
                            }
                        }
                    })),
                TextFormField::create('path')
                    ->label('radio.acp.stream.option.endpoint.path')
                    ->description('radio.acp.stream.option.endpoint.path.description')
                    ->required()
                    ->maximumLength(255)
                    ->addValidator(new FormFieldValidator('uniqueness', function (TextFormField $formField) {
                        $value = $formField->getSaveValue();
                        $value = FileUtil::addLeadingSlash($value);

                        if (\mb_strlen($value) >= 2) {
                            $value = FileUtil::removeTrailingSlash($value);
                        }

                        $formField->value($value);

                        if (
                            $this->formObject === null ||
                            $this->formObject->path !== $formField->getSaveValue()
                        ) {
                            $list = new StreamEndpointList();
                            $list->getConditionBuilder()->add('path = ?', [$formField->getSaveValue()]);
                            $list->getConditionBuilder()->add('streamID = ?', [$this->stream->streamID]);
                            if ($list->countObjects() > 0) {
                                $formField->addValidationError(
                                    new FormFieldValidationError(
                                        'notUnique',
                                        'radio.acp.stream.option.endpoint.path.error.notUnique'
                                    )
                                );
                            }
                        }

                        if (empty($formField->getSaveValue())) {
                            $formField->addValidationError(
                                new FormFieldValidationError('empty')
                            );
                        }
                    })),
                ShowOrderFormField::create()
                    ->description('radio.acp.stream.option.endpoint.showOrder.description')
                    ->options(new StreamEndpointList()),
            ]);
        $dataTab->appendChild($dataContainer);

        // create option container
        $optionGeneralContainer = FormContainer::create('optionGeneralContainer')
            ->appendChildren([
                PasswordFormField::create('endpoint_streamPassword')
                    ->label('radio.acp.stream.option.endpoint.streamPassword')
                    ->description('radio.acp.stream.option.endpoint.streamPassword.description'),
                PasswordFormField::create('endpoint_streamAdminPassword')
                    ->label('radio.acp.stream.option.endpoint.streamAdminPassword')
                    ->description('radio.acp.stream.option.endpoint.streamAdminPassword.description'),
                IntegerFormField::create('endpoint_streamMaxUser')
                    ->label('radio.acp.stream.option.endpoint.streamMaxUser')
                    ->description('radio.acp.stream.option.endpoint.streamMaxUser.description')
                    ->minimum(1)
                    ->maximum($this->stream->getStreamConfig('maxUser'))
                    ->value(1),
            ]);
        $optionsTab->appendChild($optionGeneralContainer);


        $encoderFormField = SingleSelectionFormField::create('endpoint_encoder')
            ->label('radio.acp.stream.option.endpoint.encoder')
            ->description('radio.acp.stream.option.endpoint.encoder.description')
            ->available($this->stream->getStreamConfig('transEnable'))
            ->options([
                'mp3' => 'radio.acp.stream.option.endpoint.encoder.mp3',
                'aacp' => 'radio.acp.stream.option.endpoint.encoder.aacp',
            ])
            ->value('aacp');

        $optionEndcoderContainer = FormContainer::create('optionEndcoderContainer')
            ->label('radio.acp.stream.option.endpoint.container.endcoder')
            ->appendChildren([
                $encoderFormField,
                SingleSelectionFormField::create('endpoint_bitrate')
                    ->label('radio.acp.stream.option.endpoint.bitrate')
                    ->description('radio.acp.stream.option.endpoint.bitrate.description')
                    ->available($this->stream->getStreamConfig('transEnable'))
                    ->options([
                        '16000' => '16000',
                        '32000' => '32000',
                        '40000' => '40000',
                        '48000' => '48000',
                        '56000' => '56000',
                        '64000' => '64000',
                        '80000' => '80000',
                        '96000' => '96000',
                        '112000' => '112000',
                        '128000' => '128000',
                        '160000' => '160000',
                        '192000' => '192000',
                        '224000' => '224000',
                        '256000' => '256000',
                        '320000' => '320000',
                    ])
                    ->value('96000'),
                SingleSelectionFormField::create('endpoint_sampleRate')
                    ->label('radio.acp.stream.option.endpoint.sampleRate')
                    ->description('radio.acp.stream.option.endpoint.sampleRate.description')
                    ->available($this->stream->getStreamConfig('transEnable'))
                    ->options([
                        '8000' => '8000',
                        '11025' => '11025',
                        '12000' => '12000',
                        '16000' => '16000',
                        '22025' => '22025',
                        '24000' => '24000',
                        '32000' => '32000',
                        '44100' => '44100',
                        '48000' => '48000',
                    ])
                    ->value('44100'),
                SingleSelectionFormField::create('endpoint_mp3Quality')
                    ->label('radio.acp.stream.option.endpoint.mp3Quality')
                    ->description('radio.acp.stream.option.endpoint.mp3Quality.description')
                    ->available($this->stream->getStreamConfig('transEnable'))
                    ->options([
                        '0' => 'radio.acp.stream.option.endpoint.mp3Quality.fast',
                        '1' => 'radio.acp.stream.option.endpoint.mp3Quality.high',
                    ])
                    ->value('0')
                    ->addDependency(
                        ValueFormFieldDependency::create('encoder')
                            ->field($encoderFormField)
                            ->values(['mp3'])
                    ),
                SingleSelectionFormField::create('endpoint_mp3Mode')
                    ->label('radio.acp.stream.option.endpoint.mp3Mode')
                    ->description('radio.acp.stream.option.endpoint.mp3Mode.description')
                    ->available($this->stream->getStreamConfig('transEnable'))
                    ->options([
                        '0' => 'radio.acp.stream.option.endpoint.mp3Mode0',
                        '1' => 'radio.acp.stream.option.endpoint.mp3Mode1',
                        '2' => 'radio.acp.stream.option.endpoint.mp3Mode2',
                        '3' => 'radio.acp.stream.option.endpoint.mp3Mode3',
                        '4' => 'radio.acp.stream.option.endpoint.mp3Mode4',
                        '5' => 'radio.acp.stream.option.endpoint.mp3Mode5',
                    ])
                    ->value('0')
                    ->addDependency(
                        ValueFormFieldDependency::create('encoder')
                            ->field($encoderFormField)
                            ->values(['mp3'])
                    ),

            ]);
        $optionsTab->appendChild($optionEndcoderContainer);

        $optionRelayContainer = FormContainer::create('optionRelayContainer')
            ->label('radio.acp.stream.option.endpoint.container.relay')
            ->appendChildren([
                UrlFormField::create('endpoint_streamRelayURL')
                ->label('radio.acp.stream.option.endpoint.streamRelayURL')
                ->description('radio.acp.stream.option.endpoint.streamRelayURL.description')
                ->available($this->stream->getStreamConfig('streamAllowRelay')),
            ]);
        $optionsTab->appendChild($optionRelayContainer);
    }

    /**
     * @inheritDoc
     */
    protected function finalizeForm(): void
    {
        foreach ($this->availableOptions as $property => $isDataProperty) {
            $this->form->getDataHandler()->addProcessor(
                new StreamOptionFormDataProcessor($property, 'endpoint_', $isDataProperty)
            );
        }

        $this->form->getDataHandler()->addProcessor(
            new CustomFormDataProcessor(
                'customs',
                function (IFormDocument $document, array $parameters) {
                    if (isset($parameters['data']['config'])) {
                        $parameters['data']['config'] = \serialize($parameters['data']['config']);
                    }

                    $parameters['data']['streamID'] = $this->stream->streamID;

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
        if (isset($_REQUEST['id'])) $this->streamID = \intval($_REQUEST['id']);
        $this->stream = new Stream($this->streamID);
        if (!$this->stream->streamID) {
            throw new IllegalLinkException();
        }

        parent::readParameters();
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
            $parameters['id'] = $this->stream->streamID;
        }

        $this->form->action(LinkHandler::getInstance()->getControllerLink(static::class, $parameters));
    }
}
