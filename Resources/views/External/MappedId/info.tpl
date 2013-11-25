{* Purpose of this template: Display item information for previewing from other modules *}
<dl id="mappedId{$mappedId.id}">
<dt>{$mappedId->getTitleFromDisplayPattern()|notifyfilters:'oauth.filter_hooks.mappedids.filter'|htmlentities}</dt>
{if $mappedId.claimedId ne ''}<dd>{$mappedId.claimedId}</dd>{/if}
</dl>
