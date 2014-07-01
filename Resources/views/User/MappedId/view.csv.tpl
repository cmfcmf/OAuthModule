{* purpose of this template: mapped ids view csv view in user area *}
{cmfcmfoauthmoduleTemplateHeaders contentType='text/comma-separated-values; charset=iso-8859-15' asAttachment=true filename='MappedIds.csv'}
{strip}"{gt text='User id'}";"{gt text='Claimed id'}";"{gt text='Provider'}";"{gt text='Workflow state'}"
{/strip}
{foreach item='mappedId' from=$items}
{strip}
    "{usergetvar name='uname' uid=$mappedId.userId}";"{$mappedId.claimedId}";"{$mappedId.provider}";"{$item.workflowState|cmfcmfoauthmoduleObjectState:false|lower}"
{/strip}
{/foreach}
