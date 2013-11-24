{* Purpose of this template: Display item information for previewing from other modules *}
<dl id="user{$user.id}">
<dt>{$user->getTitleFromDisplayPattern()|notifyfilters:'oauth.filter_hooks.users.filter'|htmlentities}</dt>
{if $user.claimedId ne ''}<dd>{$user.claimedId}</dd>{/if}
</dl>
