{* purpose of this template: mapped ids view json view in admin area *}
{cmfcmfoauthmoduleTemplateHeaders contentType='application/json'}
[
{foreach item='item' from=$items name='mappedIds'}
    {if not $smarty.foreach.mappedIds.first},{/if}
    {$item->toJson()}
{/foreach}
]
