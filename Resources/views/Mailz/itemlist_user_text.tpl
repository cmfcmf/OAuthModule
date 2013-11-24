{* Purpose of this template: Display users in text mailings *}
{foreach item='user' from=$items}
{$user->getTitleFromDisplayPattern()}
{modurl modname='CmfcmfOAuthModule' type='user' func='display' ot=$objectType id=$user.id fqurl=true}
-----
{foreachelse}
{gt text='No users found.'}
{/foreach}
