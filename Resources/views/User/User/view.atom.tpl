{* purpose of this template: users atom feed in user area *}
{cmfcmfoauthmoduleTemplateHeaders contentType='application/atom+xml'}<?xml version="1.0" encoding="{charset assign='charset'}{if $charset eq 'ISO-8859-15'}ISO-8859-1{else}{$charset}{/if}" ?>
<feed xmlns="http://www.w3.org/2005/Atom">
{gt text='Latest users' assign='channelTitle'}
{gt text='A direct feed showing the list of users' assign='channelDesc'}
    <title type="text">{$channelTitle}</title>
    <subtitle type="text">{$channelDesc} - {$modvars.ZConfig.slogan}</subtitle>
    <author>
        <name>{$modvars.ZConfig.sitename}</name>
    </author>
{assign var='numItems' value=$items|@count}
{if $numItems}
{capture assign='uniqueID'}tag:{$baseurl|replace:'http://':''|replace:'/':''},{$items[0].createdDate|dateformat|default:$smarty.now|dateformat:'%Y-%m-%d'}:{modurl modname='CmfcmfOAuthModule' type='user' func='view' ot='user'}{/capture}
    <id>{$uniqueID}</id>
    <updated>{$items[0].updatedDate|default:$smarty.now|dateformat:'%Y-%m-%dT%H:%M:%SZ'}</updated>
{/if}
    <link rel="alternate" type="text/html" hreflang="{lang}" href="{modurl modname='CmfcmfOAuthModule' type='user' func='index' fqurl=1}" />
    <link rel="self" type="application/atom+xml" href="{php}echo substr(\System::getBaseURL(), 0, strlen(\System::getBaseURL())-1);{/php}{getcurrenturi}" />
    <rights>Copyright (c) {php}echo date('Y');{/php}, {$baseurl}</rights>

{foreach item='user' from=$items}
    <entry>
        <title type="html">{$user->getTitleFromDisplayPattern()|notifyfilters:'cmfcmfoauthmodule.filterhook.users'}</title>
        <link rel="alternate" type="text/html" href="{modurl modname='CmfcmfOAuthModule' type='user' func='view' ot='user' fqurl='1'}" />

        {capture assign='uniqueID'}tag:{$baseurl|replace:'http://':''|replace:'/':''},{$user.createdDate|dateformat|default:$smarty.now|dateformat:'%Y-%m-%d'}:{modurl modname='CmfcmfOAuthModule' type='user' func='view' ot='user'}{/capture}
        <id>{$uniqueID}</id>
        {if isset($user.updatedDate) && $user.updatedDate ne null}
            <updated>{$user.updatedDate|dateformat:'%Y-%m-%dT%H:%M:%SZ'}</updated>
        {/if}
        {if isset($user.createdDate) && $user.createdDate ne null}
            <published>{$user.createdDate|dateformat:'%Y-%m-%dT%H:%M:%SZ'}</published>
        {/if}
        {if isset($user.createdUserId)}
            {usergetvar name='uname' uid=$user.createdUserId assign='cr_uname'}
            {usergetvar name='name' uid=$user.createdUserId assign='cr_name'}
            <author>
               <name>{$cr_name|default:$cr_uname}</name>
               <uri>{usergetvar name='_UYOURHOMEPAGE' uid=$user.createdUserId assign='homepage'}{$homepage|default:'-'}</uri>
               <email>{usergetvar name='email' uid=$user.createdUserId}</email>
            </author>
        {/if}

        <summary type="html">
            <![CDATA[
            {$user.claimedId|truncate:150:"&hellip;"|default:'-'}
            ]]>
        </summary>
        <content type="html">
            <![CDATA[
            {$user.provider|replace:'<br>':'<br />'}
            ]]>
        </content>
    </entry>
{/foreach}
</feed>
