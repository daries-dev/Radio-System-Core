{include file='header' pageTitle='radio.acp.stream.endpoint.'|concat:$action}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}radio.acp.stream.endpoint.{$action}{/lang}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li>
                <a href="{link application='radio' controller='StreamEndopintList' id=$streamID}{/link}" class="button">
                    {icon name='list'}
                    <span>{lang}radio.acp.stream.endpoint.list{/lang}</span>
                </a>
            </li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{@$form->getHtml()}

{include file='footer'}