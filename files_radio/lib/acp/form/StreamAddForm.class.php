<?php

namespace radio\acp\form;

use radio\data\stream\StreamAction;
use radio\data\stream\StreamList;
use radio\system\form\builder\data\processor\StreamOptionFormDataProcessor;
use wcf\form\AbstractForm;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\TabFormContainer;
use wcf\system\form\builder\container\TabMenuFormContainer;
use wcf\system\form\builder\container\TabTabMenuFormContainer;
use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\field\dependency\EmptyFormFieldDependency;
use wcf\system\form\builder\field\dependency\NonEmptyFormFieldDependency;
use wcf\system\form\builder\field\dependency\ValueFormFieldDependency;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\builder\field\MultipleSelectionFormField;
use wcf\system\form\builder\field\PasswordFormField;
use wcf\system\form\builder\field\RadioButtonFormField;
use wcf\system\form\builder\field\ShowOrderFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

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
     * available shoutcast options
     */
    protected array $availableShoutcastOptions = [
        'adaptiveBufferSize' => true,
        'adminPassword' => true,
        'allowPublicRelay' => true,
        'allowRelay' => true,
        'autoDumpSourceTime' => true,
        'backupFile' => true,
        'bufferHardLimit' => true,
        'bufferType' => true,
        'fixedBufferSize' => true,
        'introFile' => true,
        'maxHeaderLineCount' => true,
        'maxHeaderLineSize' => true,
        'maxHTTPRedirects' => true,
        'maxUser' => true,
        'nameLookups' => true,
        'password' => true,
        'publicServer' => true,
        'relayConnectRetries' => true,
        'relayReconnectTime' => true,
        'shoutcastDebug' => true,
        'shoutcastDebugEntry' => false,
        'songHistory' => true,
        'sslCertificateFile' => true,
        'sslCertificateKeyFile' => true,
    ];

    /**
     * available transcoder options
     */
    protected array $availableTranscoderOptions = [
        'transEnable'  => true,
        'adminPassword' => true,
        'adminPort' => true,
        'adminUser' => true,
        'autoDumpSourceTime' => true,
        'djCapture' => true,
        'djPort' => true,
        'djPort2' => true,
        'public' => true,
        'transcoderDebug' => true,
        'transcoderDebugEntry' => false,
    ];

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
        $shoutcastTab = TabFormContainer::create('shoutcastTab');
        $shoutcastTab->label('radio.acp.stream.option.shoutcast.category');
        $optionsTab->appendChild($shoutcastTab);

        $allowRelay = BooleanFormField::create('shoutcast_allowRelay')
            ->label('radio.acp.stream.option.shoutcast.allowRelay')
            ->description('radio.acp.stream.option.shoutcast.allowRelay.description')
            ->value(1);

        $bufferType = BooleanFormField::create('shoutcast_bufferType')
            ->label('radio.acp.stream.option.shoutcast.bufferType')
            ->description('radio.acp.stream.option.shoutcast.bufferType.description')
            ->value(0);

        $debug = RadioButtonFormField::create('shoutcast_shoutcastDebug')
            ->description('radio.acp.stream.option.shoutcast.debug.description')
            ->options([
                'none' => 'radio.acp.stream.option.shoutcast.debug.none',
                'all' => 'radio.acp.stream.option.shoutcast.debug.all',
                'custom' => 'radio.acp.stream.option.shoutcast.debug.custom'
            ])
            ->addClass('floated')
            ->value('none');

        $shoutcastTab->appendChildren([
            FormContainer::create('networkingSection')
                ->appendChildren([
                    PasswordFormField::create('shoutcast_password')
                        ->label('radio.acp.stream.option.shoutcast.password')
                        ->description('radio.acp.stream.option.shoutcast.password.description')
                        ->required()
                        ->maximumLength(255),
                    PasswordFormField::create('shoutcast_adminPassword')
                        ->label('radio.acp.stream.option.shoutcast.adminPassword')
                        ->description('radio.acp.stream.option.shoutcast.adminPassword.description')
                        ->required()
                        ->maximumLength(255),
                    BooleanFormField::create('shoutcast_nameLookups')
                        ->label('radio.acp.stream.option.shoutcast.nameLookups')
                        ->description('radio.acp.stream.option.shoutcast.nameLookups.description')
                        ->value(0),
                    IntegerFormField::create('shoutcast_autoDumpSourceTime')
                        ->label('radio.acp.stream.option.shoutcast.autoDumpSourceTime')
                        ->description('radio.acp.stream.option.shoutcast.autoDumpSourceTime.description')
                        ->suffix('wcf.acp.option.suffix.seconds')
                        ->minimum(0)
                        ->value(30),
                    IntegerFormField::create('shoutcast_maxHeaderLineSize')
                        ->label('radio.acp.stream.option.shoutcast.maxHeaderLineSize')
                        ->description('radio.acp.stream.option.shoutcast.maxHeaderLineSize.description')
                        ->minimum(0)
                        ->value(2048),
                    IntegerFormField::create('shoutcast_maxHeaderLineCount')
                        ->label('radio.acp.stream.option.shoutcast.maxHeaderLineCount')
                        ->description('radio.acp.stream.option.shoutcast.maxHeaderLineCount.description')
                        ->minimum(0)
                        ->value(100),

                ]),
            FormContainer::create('relaySection')
                ->label('radio.acp.stream.option.shoutcast.category.relay')
                ->appendChildren([
                    $allowRelay,
                    BooleanFormField::create('shoutcast_allowPublicRelay')
                        ->label('radio.acp.stream.option.shoutcast.allowPublicRelay')
                        ->description('radio.acp.stream.option.shoutcast.allowPublicRelay.description')
                        ->value(1)
                        ->addDependency(
                            NonEmptyFormFieldDependency::create('allowRelay')
                                ->field($allowRelay)
                        ),
                    IntegerFormField::create('shoutcast_relayReconnectTime')
                        ->label('radio.acp.stream.option.shoutcast.relayReconnectTime')
                        ->description('radio.acp.stream.option.shoutcast.relayReconnectTime.description')
                        ->suffix('wcf.acp.option.suffix.seconds')
                        ->minimum(0)
                        ->value(30)
                        ->addDependency(
                            NonEmptyFormFieldDependency::create('allowRelay')
                                ->field($allowRelay)
                        ),
                    IntegerFormField::create('shoutcast_relayConnectRetries')
                        ->label('radio.acp.stream.option.shoutcast.relayConnectRetries')
                        ->description('radio.acp.stream.option.shoutcast.relayConnectRetries.description')
                        ->minimum(0)
                        ->value(3)
                        ->addDependency(
                            NonEmptyFormFieldDependency::create('allowRelay')
                                ->field($allowRelay)
                        ),
                    IntegerFormField::create('shoutcast_maxHTTPRedirects')
                        ->label('radio.acp.stream.option.shoutcast.maxHTTPRedirects')
                        ->description('radio.acp.stream.option.shoutcast.maxHTTPRedirects.description')
                        ->minimum(0)
                        ->value(5)
                        ->addDependency(
                            NonEmptyFormFieldDependency::create('allowRelay')
                                ->field($allowRelay)
                        ),
                ]),
            FormContainer::create('sslSection')
                ->label('radio.acp.stream.option.shoutcast.category.ssl')
                ->description('radio.acp.stream.option.shoutcast.category.ssl.description')
                ->appendChildren([
                    TextFormField::create('shoutcast_sslCertificateFile')
                        ->label('radio.acp.stream.option.shoutcast.sslCertificateFile')
                        ->description('radio.acp.stream.option.shoutcast.sslCertificateFile.description')
                        ->maximumLength(255),
                    TextFormField::create('shoutcast_sslCertificateKeyFile')
                        ->label('radio.acp.stream.option.shoutcast.sslCertificateKeyFile')
                        ->description('radio.acp.stream.option.shoutcast.sslCertificateKeyFile.description')
                        ->maximumLength(255),
                ]),
            FormContainer::create('introBackupSection')
                ->label('radio.acp.stream.option.shoutcast.category.introBackupSection')
                ->description('radio.acp.stream.option.shoutcast.category.introBackupSection.description')
                ->appendChildren([
                    TextFormField::create('shoutcast_introFile')
                        ->label('radio.acp.stream.option.shoutcast.introFile')
                        ->description('radio.acp.stream.option.shoutcast.introFile.description')
                        ->maximumLength(255),
                    TextFormField::create('shoutcast_backupFile')
                        ->label('radio.acp.stream.option.shoutcast.backupFile')
                        ->description('radio.acp.stream.option.shoutcast.backupFile.description')
                        ->maximumLength(255),
                ]),
            FormContainer::create('networkBufferSection')
                ->label('radio.acp.stream.option.shoutcast.category.networkBuffer')
                ->appendChildren([
                    $bufferType,
                    IntegerFormField::create('shoutcast_adaptiveBufferSize')
                        ->label('radio.acp.stream.option.shoutcast.adaptiveBufferSize')
                        ->description('radio.acp.stream.option.shoutcast.adaptiveBufferSize.description')
                        ->suffix('wcf.acp.option.suffix.seconds')
                        ->minimum(0)
                        ->value(1)
                        ->addDependency(
                            NonEmptyFormFieldDependency::create('bufferType')
                                ->field($bufferType)
                        ),
                    IntegerFormField::create('shoutcast_fixedBufferSize')
                        ->label('radio.acp.stream.option.shoutcast.fixedBufferSize')
                        ->description('radio.acp.stream.option.shoutcast.fixedBufferSize.description')
                        ->minimum(0)
                        ->value(1048576)
                        ->addDependency(
                            EmptyFormFieldDependency::create('bufferType')
                                ->field($bufferType)
                        ),
                    IntegerFormField::create('shoutcast_bufferHardLimit')
                        ->label('radio.acp.stream.option.shoutcast.bufferHardLimit')
                        ->description('radio.acp.stream.option.shoutcast.bufferHardLimit.description')
                        ->minimum(0)
                        ->value(16777216),
                ]),
            FormContainer::create('servDebugSection')
                ->label('radio.acp.stream.option.shoutcast.category.debug')
                ->appendChildren([
                    $debug,
                    MultipleSelectionFormField::create('shoutcast_shoutcastDebugEntry')
                        ->options(static function () {
                            return [
                                'yp1debug' => 'radio.acp.stream.option.shoutcast.debug.yp1debug',
                                'yp2debug' => 'radio.acp.stream.option.shoutcast.debug.yp2debug',
                                'shoutcastsourcedebug' => 'radio.acp.stream.option.shoutcast.debug.shoutcastsourcedebug',
                                'uvox2sourcedebug' => 'radio.acp.stream.option.shoutcast.debug.uvox2sourcedebug',
                                'shoutcast2clientdebug' => 'radio.acp.stream.option.shoutcast.debug.shoutcast2clientdebug',
                                'relayshoutcastdebug' => 'radio.acp.stream.option.shoutcast.debug.relayshoutcastdebug',
                                'relayuvoxdebug' => 'radio.acp.stream.option.shoutcast.debug.relayuvoxdebug',
                                'relaydebug' => 'radio.acp.stream.option.shoutcast.debug.relaydebug',
                                'streamdatadebug' => 'radio.acp.stream.option.shoutcast.debug.streamdatadebug',
                                'httpstyledebug' => 'radio.acp.stream.option.shoutcast.debug.httpstyledebug',
                                'statsdebug' => 'radio.acp.stream.option.shoutcast.debug.statsdebug',
                                'microserverdebug' => 'radio.acp.stream.option.shoutcast.debug.microserverdebug',
                                'threadrunnerdebug' => 'radio.acp.stream.option.shoutcast.debug.threadrunnerdebug',
                                'rtmpclientdebug' => 'radio.acp.stream.option.shoutcast.debug.rtmpclientdebug',
                                'admetricsdebug' => 'radio.acp.stream.option.shoutcast.debug.admetricsdebug',
                            ];
                        })
                        ->addDependency(
                            ValueFormFieldDependency::create('debug')
                                ->field($debug)
                                ->values(['custom'])
                        ),
                ]),
            FormContainer::create('miscellaneousSection')
                ->label('radio.acp.stream.option.shoutcast.category.miscellaneous')
                ->appendChildren([
                    IntegerFormField::create('shoutcast_songHistory')
                        ->label('radio.acp.stream.option.shoutcast.songHistory')
                        ->description('radio.acp.stream.option.shoutcast.songHistory.description')
                        ->minimum(1)
                        ->value(20),
                    IntegerFormField::create('shoutcast_maxUser')
                        ->label('radio.acp.stream.option.shoutcast.maxUser')
                        ->description('radio.acp.stream.option.shoutcast.maxUser.description')
                        ->minimum(1)
                        ->value(100),
                    SingleSelectionFormField::create('shoutcast_publicServer')
                        ->label('radio.acp.stream.option.shoutcast.publicServer')
                        ->description('radio.acp.stream.option.shoutcast.publicServer.description')
                        ->options([
                            'default' => 'radio.acp.stream.option.shoutcast.publicServer.default',
                            'always' => 'radio.acp.stream.option.shoutcast.publicServer.always',
                            'never' => 'radio.acp.stream.option.shoutcast.publicServer.never'
                        ])
                        ->value('default'),
                ]),
        ]);

        $transcoderTab = TabFormContainer::create('transcoderTab');
        $transcoderTab->label('radio.acp.stream.option.transcoder.category');
        $optionsTab->appendChild($transcoderTab);

        $transEnable = BooleanFormField::create('transcoder_transEnable')
            ->label('radio.acp.stream.option.transcoder.transEnable');

        $transDebug = RadioButtonFormField::create('transcoder_transcoderDebug')
            ->description('radio.acp.stream.option.transcoder.debug.description')
            ->options([
                'none' => 'radio.acp.stream.option.transcoder.debug.none',
                'all' => 'radio.acp.stream.option.transcoder.debug.all',
                'custom' => 'radio.acp.stream.option.transcoder.debug.custom'
            ])
            ->addClass('floated')
            ->value('none')
            ->addDependency(
                NonEmptyFormFieldDependency::create('transEnable')
                    ->field($transEnable)
            );

        $transcoderTab->appendChildren([
            FormContainer::create('transcoderSelection')
                ->appendChildren([
                    $transEnable,
                    BooleanFormField::create('transcoder_public')
                        ->label('radio.acp.stream.option.transcoder.public')
                        ->description('radio.acp.stream.option.transcoder.public.description')
                        ->addDependency(
                            NonEmptyFormFieldDependency::create('transEnable')
                                ->field($transEnable)
                        ),
                ]),
            FormContainer::create('adminSection')
                ->label('radio.acp.stream.option.transcoder.category.admin')
                ->appendChildren([
                    IntegerFormField::create('transcoder_adminPort')
                        ->label('radio.acp.stream.option.transcoder.adminPort')
                        ->description('radio.acp.stream.option.transcoder.adminPort.description')
                        ->required()
                        ->minimum(0)
                        ->value(0),
                    TextFormField::create('transcoder_adminUser')
                        ->label('radio.acp.stream.option.transcoder.adminUser')
                        ->description('radio.acp.stream.option.transcoder.adminUser.description')
                        ->required()
                        ->value('admin'),
                    PasswordFormField::create('transcoder_adminPassword')
                        ->label('radio.acp.stream.option.transcoder.adminPassword')
                        ->description('radio.acp.stream.option.transcoder.adminPassword.description')
                        ->required(),
                ])
                ->addDependency(
                    NonEmptyFormFieldDependency::create('transEnable')
                        ->field($transEnable)
                ),
            FormContainer::create('djSupportSection')
                ->label('radio.acp.stream.option.transcoder.category.djSupport')
                ->appendChildren([
                    IntegerFormField::create('transcoder_djPort')
                        ->label('radio.acp.stream.option.transcoder.djPort')
                        ->description('radio.acp.stream.option.transcoder.djPort.description')
                        ->minimum(0)
                        ->value(0),
                    IntegerFormField::create('transcoder_djPort2')
                        ->label('radio.acp.stream.option.transcoder.djPort2')
                        ->description('radio.acp.stream.option.transcoder.djPort2.description')
                        ->minimum(0)
                        ->value(0),
                    IntegerFormField::create('transcoder_autoDumpSourceTime')
                        ->label('radio.acp.stream.option.transcoder.autoDumpSourceTime')
                        ->description('radio.acp.stream.option.transcoder.autoDumpSourceTime.description')
                        ->minimum(0)
                        ->value(30),
                    BooleanFormField::create('transcoder_djCapture')
                        ->label('radio.acp.stream.option.transcoder.djCapture')
                        ->description('radio.acp.stream.option.transcoder.djCapture.description')
                        ->value(1),

                ])
                ->addDependency(
                    NonEmptyFormFieldDependency::create('transEnable')
                        ->field($transEnable)
                ),
            FormContainer::create('transDebugSection')
                ->label('radio.acp.stream.option.transcoder.category.debug')
                ->appendChildren([
                    $transDebug,
                    MultipleSelectionFormField::create('transcoder_transcoderDebugEntry')
                        ->options(function () {
                            return [
                                'shuffledebug' => 'radio.acp.stream.option.transcoder.debug.shuffledebug',
                                'shoutcastdebug' => 'radio.acp.stream.option.transcoder.debug.shoutcastdebug',
                                'uvoxdebug' => 'radio.acp.stream.option.transcoder.debug.uvoxdebug',
                                'gaindebug' => 'radio.acp.stream.option.transcoder.debug.gaindebug',
                                'playlistdebug' => 'radio.acp.stream.option.transcoder.debug.playlistdebug',
                                'mp3encdebug' => 'radio.acp.stream.option.transcoder.debug.mp3encdebug',
                                'mp3decdebug' => 'radio.acp.stream.option.transcoder.debug.mp3decdebug',
                                'resamplerdebug' => 'radio.acp.stream.option.transcoder.debug.resamplerdebug',
                                'rgcalcdebug' => 'radio.acp.stream.option.transcoder.debug.rgcalcdebug',
                                'apidebug' => 'radio.acp.stream.option.transcoder.debug.apidebug',
                                'calendardebug' => 'radio.acp.stream.option.transcoder.debug.calendardebug',
                                'capturedebug' => 'radio.acp.stream.option.transcoder.debug.capturedebug',
                                'djdebug' => 'radio.acp.stream.option.transcoder.debug.djdebug',
                                'fileconverterdebug' => 'radio.acp.stream.option.transcoder.debug.fileconverterdebug',
                                'sourcerelaydebug' => 'radio.acp.stream.option.transcoder.debug.sourcerelaydebug',
                                'sourceandendpointmanagerdebug' => 'radio.acp.stream.option.transcoder.debug.sourceandendpointmanagerdebug'
                            ];
                        })
                        ->addDependency(
                            ValueFormFieldDependency::create('transDebug')
                                ->field($transDebug)
                                ->values(['custom'])
                        ),
                ]),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function finalizeForm()
    {
        foreach ($this->availableShoutcastOptions as $property => $isDataProperty) {
            $this->form->getDataHandler()->addProcessor(
                new StreamOptionFormDataProcessor($property, 'shoutcast_', $isDataProperty)
            );
        }

        foreach ($this->availableTranscoderOptions as $property => $isDataProperty) {
            $this->form->getDataHandler()->addProcessor(
                new StreamOptionFormDataProcessor($property, 'transcoder_', $isDataProperty)
            );
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
}
