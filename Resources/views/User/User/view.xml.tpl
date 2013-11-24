{* purpose of this template: users view xml view in user area *}
{cmfcmfoauthmoduleTemplateHeaders contentType='text/xml'}<?xml version="1.0" encoding="{charset}" ?>
<users>
{foreach item='item' from=$items}
    {include file='user/user/include.xml'}
{foreachelse}
    <noUser />
{/foreach}
</users>
