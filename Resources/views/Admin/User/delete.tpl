{* purpose of this template: users delete confirmation view in admin area *}
{include file='Admin/header.tpl'}
<div class="cmfcmfoauthmodule-user cmfcmfoauthmodule-delete">
    {gt text='Delete user' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    <h3>
        <span class="fa fa-trash-o"></span>
        {$templateTitle}
    </h3>

    <p class="alert alert-warningmsg">{gt text='Do you really want to delete this user ?'}</p>

    <form class="form-horizontal" action="{modurl modname='CmfcmfOAuthModule' type='admin' func='delete' ot='user' id=$user.id}" method="post" role="form">
        <div>
            <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
            <input type="hidden" id="confirmation" name="confirmation" value="1" />
            <fieldset>
                <legend>{gt text='Confirmation prompt'}</legend>
                <div class="form-group form-buttons">
                <div class="col-lg-offset-3 col-lg-9">
                    {gt text='Delete' assign='deleteTitle'}
                    {button src='14_layer_deletelayer.png' set='icons/small' text=$deleteTitle title=$deleteTitle class='btn btn-danger'}
                    <a href="{modurl modname='CmfcmfOAuthModule' type='admin' func='view' ot='user'}" class="btn btn-default" role="button"><span class="fa fa-times"></span> {gt text='Cancel'}</a>
                </div>
                </div>
            </fieldset>

            {notifydisplayhooks eventname='cmfcmfoauthmodule.ui_hooks.users.form_delete' id="`$user.id`" assign='hooks'}
            {foreach key='providerArea' item='hook' from=$hooks}
            <fieldset>
                <legend>{$hookName}</legend>
                {$hook}
            </fieldset>
            {/foreach}
        </div>
    </form>
</div>
{include file='Admin/footer.tpl'}
