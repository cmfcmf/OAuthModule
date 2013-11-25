{* Purpose of this template: Display one certain mapped id within an external context *}
<div id="mappedId{$mappedId.id}" class="cmfcmfoauthmodule-external-mappedid">
{if $displayMode eq 'link'}
    <p class="cmfcmfoauthmodule-external-link">
    <a href="{modurl modname='CmfcmfOAuthModule' type='user' func='display' ot='mappedId' id=$mappedId.id}" title="{$mappedId->getTitleFromDisplayPattern()|replace:"\"":""}">
    {$mappedId->getTitleFromDisplayPattern()|notifyfilters:'oauth.filter_hooks.mappedids.filter'}
    </a>
    </p>
{/if}
{checkpermissionblock component='CmfcmfOAuthModule::' instance='::' level='ACCESS_EDIT'}
    {if $displayMode eq 'embed'}
        <p class="cmfcmfoauthmodule-external-title">
            <strong>{$mappedId->getTitleFromDisplayPattern()|notifyfilters:'oauth.filter_hooks.mappedids.filter'}</strong>
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
            {if $mappedId.claimedId ne ''}{$mappedId.claimedId}<br />{/if}
        </p>
    *}
{/if}
</div>
