{* Purpose of this template: Display users in html mailings *}
{*
<ul>
{foreach item='user' from=$items}
    <li>
        <a href="{modurl modname='CmfcmfOAuthModule' type='user' func='view' fqurl=true}
        ">{$user->getTitleFromDisplayPattern()}
        </a>
    </li>
{foreachelse}
    <li>{gt text='No users found.'}</li>
{/foreach}
</ul>
*}

{include file='ContentType/itemlist_user_display_description.tpl'}
