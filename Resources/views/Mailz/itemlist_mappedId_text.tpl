{* Purpose of this template: Display mapped ids in text mailings *}
{foreach item='mappedId' from=$items}
{$mappedId->getTitleFromDisplayPattern()}
{modurl modname='CmfcmfOAuthModule' type='user' func='display' ot=$objectType id=$mappedId.id fqurl=true}
-----
{foreachelse}
{gt text='No mapped ids found.'}
{/foreach}
