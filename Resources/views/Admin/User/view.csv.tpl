{* purpose of this template: users view csv view in admin area *}
{cmfcmfoauthmoduleTemplateHeaders contentType='text/comma-separated-values; charset=iso-8859-15' asAttachment=true filename='Users.csv'}
{strip}"{gt text='User id'}";"{gt text='Claimed id'}";"{gt text='Provider'}";"{gt text='Workflow state'}"
{/strip}
{foreach item='user' from=$items}
{strip}
    "{usergetvar name='uname' uid=$user.userId}";"{$user.claimedId}";"{$user.provider}";"{$item.workflowState|cmfcmfoauthmoduleObjectState:false|lower}"
{/strip}
{/foreach}
