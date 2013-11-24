{* Purpose of this template: Display users within an external context *}
<dl>
    {foreach item='user' from=$items}
        <dt>{$user->getTitleFromDisplayPattern()}</dt>
        {if $user.claimedId}
            <dd>{$user.claimedId|truncate:200:"..."}</dd>
        {/if}
        <dd><a href="{modurl modname='CmfcmfOAuthModule' type='user' func='display' ot=$objectType id=$user.id}">{gt text='Read more'}</a>
        </dd>
    {foreachelse}
        <dt>{gt text='No entries found.'}</dt>
    {/foreach}
</dl>
