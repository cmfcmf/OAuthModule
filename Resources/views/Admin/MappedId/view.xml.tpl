{* purpose of this template: mapped ids view xml view in admin area *}
{cmfcmfoauthmoduleTemplateHeaders contentType='text/xml'}<?xml version="1.0" encoding="{charset}" ?>
<mappedIds>
{foreach item='item' from=$items}
    {include file='admin/mappedId/include.xml'}
{foreachelse}
    <noMappedId />
{/foreach}
</mappedIds>
