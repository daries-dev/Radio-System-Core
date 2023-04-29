{include file='header' pageTitle='radio.acp.stream.'|concat:$action}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}radio.acp.stream.{$action}{/lang}</h1>
    </div>

    <nav class="contentHeaderNavigation">
        <ul>
            <li>
                <a href="{link application='radio' controller='StreamList'}{/link}" class="button">
                    {icon name='list'}
                    <span>{lang}radio.acp.stream.list{/lang}</span>
                </a>
            </li>

            {event name='contentHeaderNavigation'}
        </ul>
    </nav>
</header>

{@$form->getHtml()}

{include file='footer'}