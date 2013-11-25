{* Purpose of this template: Display mapped ids within an external context *}
<dl>
    {foreach item='mappedId' from=$items}
        <dt>{$mappedId->getTitleFromDisplayPattern()}</dt>
        {if $mappedId.claimedId}
            <dd>{$mappedId.claimedId|truncate:200:"..."}</dd>
        {/if}
        <dd><a href="{modurl modname='CmfcmfOAuthModule' type='user' func='display' ot=$objectType id=$mappedId.id}">{gt text='Read more'}</a>
        </dd>
    {foreachelse}
        <dt>{gt text='No entries found.'}</dt>
    {/foreach}
</dl>
