{* Purpose of this template: Display one certain user within an external context *}
<div id="user{$user.id}" class="cmfcmfoauthmodule-external-user">
{if $displayMode eq 'link'}
    <p class="cmfcmfoauthmodule-external-link">
    <a href="{modurl modname='CmfcmfOAuthModule' type='user' func='display' ot='user' id=$user.id}" title="{$user->getTitleFromDisplayPattern()|replace:"\"":""}">
    {$user->getTitleFromDisplayPattern()|notifyfilters:'oauth.filter_hooks.users.filter'}
    </a>
    </p>
{/if}
{checkpermissionblock component='CmfcmfOAuthModule::' instance='::' level='ACCESS_EDIT'}
    {if $displayMode eq 'embed'}
        <p class="cmfcmfoauthmodule-external-title">
            <strong>{$user->getTitleFromDisplayPattern()|notifyfilters:'oauth.filter_hooks.users.filter'}</strong>
        </p>
    {/if}
{/checkpermissionblock}

{if $displayMode eq 'link'}
{elseif $displayMode eq 'embed'}
    <div class="cmfcmfoauthmodule-external-snippet">
        &nbsp;
    </div>

    {* you can distinguish the context like this: *}
    {*if $source eq 'contentType'}
        ...
    {elseif $source eq 'scribite'}
        ...
    {/if*}

    {* you can enable more details about the item: *}
    {*
        <p class="cmfcmfoauthmodule-external-description">
            {if $user.claimedId ne ''}{$user.claimedId}<br />{/if}
        </p>
    *}
{/if}
</div>
