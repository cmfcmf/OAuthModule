{* purpose of this template: users view view in user area *}
{include file='User/header.tpl'}
<div class="cmfcmfoauthmodule-user cmfcmfoauthmodule-view">
    {gt text='User list' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    <h2>{$templateTitle}</h2>

    {assign var='own' value=0}
    {if isset($showOwnEntries) && $showOwnEntries eq 1}
        {assign var='own' value=1}
    {/if}
    {assign var='all' value=0}
    {if isset($showAllEntries) && $showAllEntries eq 1}
        {gt text='Back to paginated view' assign='linkTitle'}
        <a href="{modurl modname='CmfcmfOAuthModule' type='user' func='view' ot='user'}" title="{$linkTitle}" class="fa fa-table">
            {$linkTitle}
        </a>
        {assign var='all' value=1}
    {else}
        {gt text='Show all entries' assign='linkTitle'}
        <a href="{modurl modname='CmfcmfOAuthModule' type='user' func='view' ot='user' all=1}" title="{$linkTitle}" class="fa fa-table">{$linkTitle}</a>
    {/if}

    {include file='User/User/view_quickNav.tpl' all=$all own=$own}{* see template file for available options *}

    <table class="table table-striped table-bordered table-hover{* table-responsive*}">
        <colgroup>
            <col id="cWorkflowState" />
            <col id="cUserId" />
            <col id="cClaimedId" />
            <col id="cProvider" />
            <col id="cItemActions" />
        </colgroup>
        <thead>
        <tr>
            <th id="hWorkflowState" scope="col" class="text-left">
                {sortlink __linktext='State' currentsort=$sort modname='CmfcmfOAuthModule' type='user' func='view' ot='user' sort='workflowState' sortdir=$sdir all=$all own=$own workflowState=$workflowState userId=$userId searchterm=$searchterm pageSize=$pageSize}
            </th>
            <th id="hUserId" scope="col" class="text-left">
                {sortlink __linktext='User id' currentsort=$sort modname='CmfcmfOAuthModule' type='user' func='view' ot='user' sort='userId' sortdir=$sdir all=$all own=$own workflowState=$workflowState userId=$userId searchterm=$searchterm pageSize=$pageSize}
            </th>
            <th id="hClaimedId" scope="col" class="text-left">
                {sortlink __linktext='Claimed id' currentsort=$sort modname='CmfcmfOAuthModule' type='user' func='view' ot='user' sort='claimedId' sortdir=$sdir all=$all own=$own workflowState=$workflowState userId=$userId searchterm=$searchterm pageSize=$pageSize}
            </th>
            <th id="hProvider" scope="col" class="text-left">
                {sortlink __linktext='Provider' currentsort=$sort modname='CmfcmfOAuthModule' type='user' func='view' ot='user' sort='provider' sortdir=$sdir all=$all own=$own workflowState=$workflowState userId=$userId searchterm=$searchterm pageSize=$pageSize}
            </th>
            <th id="hItemActions" scope="col" class="z-order-unsorted">{gt text='Actions'}</th>
        </tr>
        </thead>
        <tbody>
    
    {foreach item='user' from=$items}
        <tr>
            <td headers="hWorkflowState" class="z-left nowrap">
                {$user.workflowState|cmfcmfoauthmoduleObjectState}
            </td>
            <td headers="hUserId" class="z-left">
                {$user.userId|notifyfilters:'cmfcmfoauthmodule.filterhook.users'}
            </td>
            <td headers="hClaimedId" class="z-left">
                {$user.claimedId}
            </td>
            <td headers="hProvider" class="z-left">
                {$user.provider}
            </td>
            <td id="itemActions{$user.id}" headers="hItemActions" class="actions nowrap z-w02">
                {if count($user._actions) gt 0}
                    {foreach item='option' from=$user._actions}
                        <a href="{$option.url.type|cmfcmfoauthmoduleActionUrl:$option.url.func:$option.url.arguments}" title="{$option.linkTitle|safetext}"{if $option.icon eq 'zoom-in'} target="_blank"{/if} class="fa fa-{$option.icon}" data-linktext="{$option.linkText|safetext}"></a>
                    {/foreach}
                    {icon id="itemActions`$user.id`Trigger" type='options' size='extrasmall' __alt='Actions' class='cursor-pointer hide'}
                    <script type="text/javascript">
                    /* <![CDATA[ */
                        document.observe('dom:loaded', function() {
                            oauthInitItemActions('user', 'view', 'itemActions{{$user.id}}');
                        });
                    /* ]]> */
                    </script>
                {/if}
            </td>
        </tr>
    {foreachelse}
        <tr class="z-datatableempty">
          <td class="text-left" colspan="5">
        {gt text='No users found.'}
          </td>
        </tr>
    {/foreach}
    
        </tbody>
    </table>
    
    {if !isset($showAllEntries) || $showAllEntries ne 1}
        {pager rowcount=$pager.numitems limit=$pager.itemsperpage display='page' modname='CmfcmfOAuthModule' type='user' func='view' ot='user'}
    {/if}

    
    {notifydisplayhooks eventname='cmfcmfoauthmodule.ui_hooks.users.display_view' urlobject=$currentUrlObject assign='hooks'}
    {foreach key='providerArea' item='hook' from=$hooks}
        {$hook}
    {/foreach}
</div>
{include file='User/footer.tpl'}
