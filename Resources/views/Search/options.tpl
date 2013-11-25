{* Purpose of this template: Display search options *}
<input type="hidden" id="cmfcmfOAuthModuleActive" name="active[CmfcmfOAuthModule]" value="1" checked="checked" />
<div>
    <input type="checkbox" id="cmfcmfOAuthModuleMappedIds" name="cmfcmfOAuthModuleSearchTypes[]" value="mappedId"{if $active_mappedId} checked="checked"{/if} />
    <label for="active_cmfcmfOAuthModuleMappedIds">{gt text='Mapped ids' domain='module_cmfcmfoauthmodule'}</label>
</div>
