{* purpose of this template: mapped ids view xml view in user area *}
{cmfcmfoauthmoduleTemplateHeaders contentType='text/xml'}<?xml version="1.0" encoding="{charset}" ?>
<mappedIds>
{foreach item='item' from=$items}
    {include file='user/mappedId/include.xml'}
{foreachelse}
    <noMappedId />
{/foreach}
</mappedIds>
