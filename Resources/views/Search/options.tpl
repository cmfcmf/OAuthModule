{* Purpose of this template: Display search options *}
<input type="hidden" id="cmfcmfOAuthModuleActive" name="active[CmfcmfOAuthModule]" value="1" checked="checked" />
<div>
    <input type="checkbox" id="cmfcmfOAuthModuleUsers" name="cmfcmfOAuthModuleSearchTypes[]" value="user"{if $active_user} checked="checked"{/if} />
    <label for="active_cmfcmfOAuthModuleUsers">{gt text='Users' domain='module_cmfcmfoauthmodule'}</label>
</div>
