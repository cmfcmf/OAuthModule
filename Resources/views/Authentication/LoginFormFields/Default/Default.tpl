<input type="hidden" name="authentication_info[supplied_id]" value="dummy" />
<div id="users_login_fields">
    {assign var='icon' value=$oAuthHelper->getIcon()}
    {if isset($icon) && !empty($icon)}
        {if !$oAuthHelper->isFontAwesomeIcon()}
            <img src="{$icon}" style="max-height: 36px;" />
        {else}
            <i class="fa {$icon} fa-3x"></i>
            <br />
        {/if}
    {/if}
    <p>{gt text="Click 'Log in' to log in with your %s Account." tag1=$oAuthHelper->getProviderDisplayName()}</p>
</div>