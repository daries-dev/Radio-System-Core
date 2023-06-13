{include file='header' pageTitle='radio.acp.stream.endpoint.list'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}radio.acp.stream.endpoint.list{/lang}</h1>
        <p class="contentHeaderDescription">{$stream->getTitle()}</p>
    </div>

    {hascontent}
    <nav class="contentHeaderNavigation">
        <ul>
            {content}
            {if $__wcf->session->getPermission('admin.radio.stream.canEditStream')}
                <li>
                    <a href="{link application='radio' controller='StreamEdit' id=$streamID}{/link}" class="button">
                        {icon name='pencil'}
                        <span>{lang}radio.acp.stream.edit{/lang}</span>
                    </a>
                </li>
            {/if}
            {if $__wcf->session->getPermission('admin.radio.stream.canAddEndpoint')}
                <li>
                    <a href="{link application='radio' controller='StreamEndpointAdd' id=$streamID}{/link}" class="button">
                        {icon name='plus'}
                        <span>{lang}radio.acp.stream.endpoint.add{/lang}</span>
                    </a>
                </li>
            {/if}

            {event name='contentHeaderNavigation'}
            {/content}
        </ul>
    </nav>
    {/hascontent}
</header>

{hascontent}
<div id="endpointList"
    class="section{if $__wcf->session->getPermission('admin.radio.stream.canEditEndpoint')} sortableListContainer{/if}">
    <ol class="endpointList sortableList jsObjectActionContainer"
        data-object-action-class-name="radio\data\stream\endpoint\StreamEndpointAction" data-object-id="0">
        {content}
        {foreach from=$endpoints item='endpoint'}
            <li class="{if $__wcf->session->getPermission('admin.radio.stream.canEditEndpoint')}sortableNode{/if} sortableNoNesting jsEndpoint jsObjectActionObject"
                data-object-id="{$endpoint->getObjectID()}">
                <span class="sortableNodeLabel">
                    <span class="title">
                        {event name='beforeTitle'}
                        {if $__wcf->session->getPermission('admin.radio.stream.canEditEndpoint')}
                            <a
                                href="{link application='radio' controller='StreamEndpointEdit' object=$endpoint}{/link}">{$endpoint->getTitle()}</a>
                        {else}
                            {$endpoint->getTitle()}
                        {/if}
                    </span>

                    <span class="statusDisplay buttons">
                        <span class="sortableNodeHandle">
                            {icon name='arrows-up-down-left-right'}
                        </span>

                        {if $__wcf->session->getPermission('admin.radio.stream.canEditEndpoint')}
                            {objectAction action="toggle" isDisabled=$endpoint->isDisabled}
                            <a href="{link application='radio' controller='StreamEndpointEdit' object=$endpoint}{/link}"
                                title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip">{icon name='pencil'}</a>
                        {/if}

                        {if $__wcf->session->getPermission('admin.radio.stream.canDeleteEndpoint')}
                            <button type="button" class="jsObjectAction jsTooltip" title="{lang}wcf.global.button.delete{/lang}"
                                data-object-action="delete" data-confirm-message="radio.acp.stream.endpoint.delete.sure">
                                {icon name='xmark'}
                            </button>
                        {/if}

                        {event name='itemButtons'}
                    </span>
                </span>
            </li>
        {/foreach}
        {/content}
    </ol>
</div>

<div class="formSubmit">
    <button type="button" class="button buttonPrimary" data-type="submit">
        {lang}wcf.global.button.saveSorting{/lang}
    </button>
</div>

{if $__wcf->session->getPermission('admin.radio.stream.canEditEndpoint')}
    <script data-relocate="true">
        require(['WoltLabSuite/Core/Ui/Sortable/List'], function(UiSortableList) {
            new UiSortableList({
                additionalParameters: {
                    streamID: {$stream->streamID}
                },
                containerId: 'endpointList',
                className: 'radio\\data\\stream\\endpoint\\StreamEndpointAction',
                isSimpleSorting: true,
            });
        });
    </script>
{/if}
{hascontentelse}
<p class="info">{lang __stream=$stream}radio.acp.stream.endpoint.noneAvailable{/lang}</p>
{/hascontent}

{include file='footer'}