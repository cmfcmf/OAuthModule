{* purpose of this template: users view view in admin area *}
{include file='Admin/header.tpl'}
<div class="cmfcmfoauthmodule-user cmfcmfoauthmodule-view">
    {gt text='User list' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    <h3>
        <span class="fa fa-list"></span>
        {$templateTitle}
    </h3>

    {assign var='own' value=0}
    {if isset($showOwnEntries) && $showOwnEntries eq 1}
        {assign var='own' value=1}
    {/if}
    {assign var='all' value=0}
    {if isset($showAllEntries) && $showAllEntries eq 1}
        {gt text='Back to paginated view' assign='linkTitle'}
        <a href="{modurl modname='CmfcmfOAuthModule' type='admin' func='view' ot='user'}" title="{$linkTitle}" class="fa fa-table">
            {$linkTitle}
        </a>
        {assign var='all' value=1}
    {else}
        {gt text='Show all entries' assign='linkTitle'}
        <a href="{modurl modname='CmfcmfOAuthModule' type='admin' func='view' ot='user' all=1}" title="{$linkTitle}" class="fa fa-table">{$linkTitle}</a>
    {/if}

    {include file='Admin/User/view_quickNav.tpl' all=$all own=$own}{* see template file for available options *}

    <form action="{modurl modname='CmfcmfOAuthModule' type='admin' func='handleSelectedEntries'}" method="post" id="usersViewForm" class="form-horizontal" role="form">
        <div>
            <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
            <input type="hidden" name="ot" value="user" />
            <table class="table table-striped table-bordered table-hover{* table-responsive*}">
                <colgroup>
                    <col id="cSelect" />
                    <col id="cWorkflowState" />
                    <col id="cUserId" />
                    <col id="cClaimedId" />
                    <col id="cProvider" />
                    <col id="cItemActions" />
                </colgroup>
                <thead>
                <tr>
                    <th id="hSelect" scope="col" align="center" valign="middle">
                        <input type="checkbox" id="toggleUsers" />
                    </th>
                    <th id="hWorkflowState" scope="col" class="text-left">
                        {sortlink __linktext='State' currentsort=$sort modname='CmfcmfOAuthModule' type='admin' func='view' ot='user' sort='workflowState' sortdir=$sdir all=$all own=$own workflowState=$workflowState userId=$userId searchterm=$searchterm pageSize=$pageSize}
                    </th>
                    <th id="hUserId" scope="col" class="text-left">
                        {sortlink __linktext='User id' currentsort=$sort modname='CmfcmfOAuthModule' type='admin' func='view' ot='user' sort='userId' sortdir=$sdir all=$all own=$own workflowState=$workflowState userId=$userId searchterm=$searchterm pageSize=$pageSize}
                    </th>
                    <th id="hClaimedId" scope="col" class="text-left">
                        {sortlink __linktext='Claimed id' currentsort=$sort modname='CmfcmfOAuthModule' type='admin' func='view' ot='user' sort='claimedId' sortdir=$sdir all=$all own=$own workflowState=$workflowState userId=$userId searchterm=$searchterm pageSize=$pageSize}
                    </th>
                    <th id="hProvider" scope="col" class="text-left">
                        {sortlink __linktext='Provider' currentsort=$sort modname='CmfcmfOAuthModule' type='admin' func='view' ot='user' sort='provider' sortdir=$sdir all=$all own=$own workflowState=$workflowState userId=$userId searchterm=$searchterm pageSize=$pageSize}
                    </th>
                    <th id="hItemActions" scope="col" class="z-order-unsorted">{gt text='Actions'}</th>
                </tr>
                </thead>
                <tbody>
            
            {foreach item='user' from=$items}
                <tr>
                    <td headers="hselect" align="center" valign="top">
                        <input type="checkbox" name="items[]" value="{$user.id}" class="users-checkbox" />
                    </td>
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
                <tr class="z-admintableempty">
                  <td class="text-left" colspan="6">
                {gt text='No users found.'}
                  </td>
                </tr>
            {/foreach}
            
                </tbody>
            </table>
            
            {if !isset($showAllEntries) || $showAllEntries ne 1}
                {pager rowcount=$pager.numitems limit=$pager.itemsperpage display='page' modname='CmfcmfOAuthModule' type='admin' func='view' ot='user'}
            {/if}
            <fieldset>
                <label for="cmfcmfOAuthModuleAction" class="col-lg-3 control-label">{gt text='With selected users'}</label>
                <div class="col-lg-9">
                <select id="cmfcmfOAuthModuleAction" name="action" class="form-control">
                    <option value="">{gt text='Choose action'}</option>
                    <option value="delete" title="{gt text='Delete content permanently.'}">{gt text='Delete'}</option>
                </select>
                </div>
                <input type="submit" value="{gt text='Submit'}" />
            </fieldset>
        </div>
    </form>

</div>
{include file='Admin/footer.tpl'}

<script type="text/javascript">
/* <![CDATA[ */
    document.observe('dom:loaded', function() {
    {{* init the "toggle all" functionality *}}
    if ($('toggleUsers') != undefined) {
        $('toggleUsers').observe('click', function (e) {
            Zikula.toggleInput('usersViewForm');
            e.stop()
        });
    }
    });
/* ]]> */
</script>
