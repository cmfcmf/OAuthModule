{* purpose of this template: users view json view in admin area *}
{cmfcmfoauthmoduleTemplateHeaders contentType='application/json'}
[
{foreach item='item' from=$items name='users'}
    {if not $smarty.foreach.users.first},{/if}
    {$item->toJson()}
{/foreach}
]
