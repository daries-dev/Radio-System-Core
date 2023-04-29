{include file='header' pageTitle='radio.acp.stream.list'}

{event name='javascriptInclude'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">
            {lang}radio.acp.stream.list{/lang}
            {if $items}<span class="badge badgeInverse">{#$items}</span>{/if}
        </h1>
    </div>

    {hascontent}
    <nav class="contentHeaderNavigation">
        <ul>
            {content}
            {if $__wcf->session->getPermission('admin.user.canAddUser')}
                <li>
                    <a href="{link application='radio' controller='StreamAdd'}{/link}" class="button">
                        {icon name='plus'}
                        <span>{lang}radio.acp.stream.add{/lang}</span>
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
<div class="paginationTop">
    {content}{pages print=true assign=pagesLinks controller="StreamList" application="radio" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}{/content}
</div>
{/hascontent}

{if $items}
    <div id="streamContainer" class="section tabularBox">
        <table class="table jsObjectActionContainer" data-object-action-class-name="radio\data\stream\StreamAction">
            <thead>
                <tr>
                    <th class="columnID columnStreamID{if $sortField == 'streamID'} active {@$sortOrder}{/if}" colspan="2">
                        <a
                            href="{link application='radio' controller='StreamList'}pageNo={@$pageNo}&sortField=streamID&sortOrder={if $sortField == 'streamID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
                            {lang}wcf.global.objectID{/lang}
                        </a>
                    </th>
                    <th class="columnTitle columnStreamName{if $sortField == 'streamname'} active {@$sortOrder}{/if}">
                        <a
                            href="{link application='radio' controller='StreamList'}pageNo={@$pageNo}&sortField=streamname&sortOrder={if $sortField == 'streamname' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
                            {lang}radio.stream.streamname{/lang}
                        </a>
                    </th>
                    <th class="columnText columnHost{if $sortField == 'host'} active {@$sortOrder}{/if}">
                        <a
                            href="{link application='radio' controller='StreamList'}pageNo={@$pageNo}&sortField=host&sortOrder={if $sortField == 'host' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
                            {lang}radio.stream.host{/lang}
                        </a>
                    </th>
                    <th class="columnDigits columnPort{if $sortField == 'port'} active {@$sortOrder}{/if}">
                        <a
                            href="{link application='radio' controller='StreamList'}pageNo={@$pageNo}&sortField=port&sortOrder={if $sortField == 'port' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">
                            {lang}radio.stream.port{/lang}
                        </a>
                    </th>

                    {event name='columnHeads'}
                </tr>
            </thead>

            <tbody class="jsReloadPageWhenEmpty">
                {foreach from=$objects item=stream}
                    <tr class="jsStreamRow jsObjectActionObject" data-object-id="{@$stream->getObjectID()}">
                        <td class="columnIcon">
                            <div class="dropdown" id="streamListDropdown{@$stream->streamID}">
                                <a href="#" class="dropdownToggle button small">
                                    {icon name='pencil'}
                                    <span>{lang}wcf.global.button.edit{/lang}</span>
                                </a>

                                <ul class="dropdownMenu">
                                    {event name='dropdownItems'}

                                    <li>
                                        <a href="{link application='radio' controller='StreamEdit' id=$stream->streamID}{/link}"
                                            class="jsEditLink">
                                            {lang}wcf.global.button.edit{/lang}
                                        </a>
                                    </li>

                                    {if $__wcf->session->getPermission('admin.radio.stream.canDeleteStream')}
                                        <li class="dropdownDivider"></li>
                                        <li>
                                            <a href="#" class="jsDelete"
                                                data-confirm-message="{lang __encode=true objectTitle=$stream->streamname}wcf.button.delete.confirmMessage{/lang}">
                                                {lang}wcf.global.button.delete{/lang}
                                            </a>
                                        </li>
                                    {/if}
                                </ul>
                            </div>
                        </td>
                        <td class="columnID columnStreamID">{@$stream->streamID}</td>
                        <td class="columnTitle columnStreamName">
                            <span class="streamname">
                                <a title="{lang}radio.acp.stream.edit{/lang}"
                                    href="{link application='radio' controller='StreamEdit' id=$stream->streamID}{/link}">
                                    {$stream->getTitle()}
                                </a>
                            </span>
                        </td>
                        <td class="columnText columnHost">{$stream->host}</td>
                        <td class="columnDigits columnPort">{$stream->port}</td>

                        {event name='columns'}
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>

    <footer class="contentFooter">
        {hascontent}
        <div class="paginationBottom">
            {content}{@$pagesLinks}{/content}
        </div>
        {/hascontent}

        {hascontent}
        <nav class="contentFooterNavigation">
            <ul>
                {content}
                {if $__wcf->session->getPermission('admin.radio.stream.canAddStream')}
                    <li>
                        <a href="{link application='radio' controller='StreamAdd'}{/link}" class="button">
                            {icon name='plus'}
                            <span>{lang}radio.acp.stream.add{/lang}</span>
                        </a>
                    </li>
                {/if}

                {event name='contentFooterNavigation'}
                {/content}
            </ul>
        </nav>
        {/hascontent}
    </footer>
{else}
    <p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}