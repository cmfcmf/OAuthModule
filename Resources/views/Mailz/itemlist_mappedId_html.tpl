{* Purpose of this template: Display mapped ids in html mailings *}
{*
<ul>
{foreach item='mappedId' from=$items}
    <li>
        <a href="{modurl modname='CmfcmfOAuthModule' type='user' func='view' fqurl=true}
        ">{$mappedId->getTitleFromDisplayPattern()}
        </a>
    </li>
{foreachelse}
    <li>{gt text='No mapped ids found.'}</li>
{/foreach}
</ul>
*}

{include file='ContentType/itemlist_mappedId_display_description.tpl'}
