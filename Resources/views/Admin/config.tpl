{* purpose of this template: module configuration *}
{include file='Admin/header.tpl'}
{pageaddvar name='javascript' value='modules/Cmfcmf/OAuthModule/Resources/public/js/CmfcmfOAuthModule.Admin.Config.js'}
{pageaddvarblock}
<style type="text/css">
    .cmfcmfoauthmodule-config .cmfcmfoauthmodule-passwordToggle {
        cursor: pointer;
    }
</style>
{/pageaddvarblock}
<div class="cmfcmfoauthmodule-config">
    {gt text='Settings' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    <h3>
        <span class="fa fa-wrench"></span>
        {$templateTitle}
    </h3>

    {form cssClass='form-horizontal' role='form' autocomplete="off"}
        {* add validation summary and a <div> element for styling the form *}
        {cmfcmfoauthmoduleFormFrame}

            <!-- Nav tabs -->
            <ul class="nav nav-tabs">
                <li><a href="#general" data-toggle="tab">{gt text='General settings'}</a></li>
                {foreach from=$providers item='provider'}
                    <li><a href="#tab{$provider->getProviderName()}" data-toggle="tab">{$provider->getProviderDisplayName()}</a></li>
                {/foreach}
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane active fade in" id="general">
                    <p class="alert alert-info">{gt text='Here you can manage all basic settings for this application.'}</p>
                    <div class="form-group">
                        {formlabel for='suggestRegistrationOnFailedLogin' __text='Suggest registration on failed login' cssClass=' col-lg-3 control-label'}
                        <div class="col-lg-9">
                            {formcheckbox id='suggestRegistrationOnFailedLogin' group='config'}
                        </div>
                    </div>
                </div>
                {foreach from=$providers item='provider'}
                    <div class="tab-pane fade" id="tab{$provider->getProviderName()}">
                        {if $provider->getApplicationRegistrationDoc()}
                            <p class="alert alert-info">{$provider->getApplicationRegistrationDoc()}</p>
                        {/if}
                        <div class="form-group">
                            {gt text='%s consumer key / id' tag1=$provider->getProviderName() assign='gtText'}
                            {formlabel for='key'|cat:$provider->getProviderName() text=$gtText cssClass=' col-lg-3 control-label'}
                            <div class="col-lg-9">
                                {gt text='Enter the %s consumer key / id' tag1=$provider->getProviderName() assign='gtText'}
                                {formtextinput id='key'|cat:$provider->getProviderName() group='config' maxLength=255 title=$gtText cssClass='form-control cmfcmfoauthmodule-consumerkey'}
                            </div>
                        </div>
                        <div class="form-group">
                            {gt text='%s secret' tag1=$provider->getProviderName() assign='gtText'}
                            {formlabel for='secret'|cat:$provider->getProviderName() text=$gtText cssClass=' col-lg-3 control-label'}
                            <div class="col-lg-9">
                                {gt text='Enter the %s secret' tag1=$provider->getProviderName() assign='gtText'}
                                <div class="input-group">
                                    <i class="fa fa-key input-group-addon cmfcmfoauthmodule-passwordToggle"></i>
                                    {formtextinput id='secret'|cat:$provider->getProviderName() group='config' maxLength=255 title=$gtText cssClass='form-control cmfcmfoauthmodule-consumersecret' textMode='password' attributes=$autocompleteAttribute}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {formlabel for='registrationProvider'|cat:$provider->getProviderName() __text='Enable for registration' cssClass=' col-lg-3 control-label'}
                            <div class="col-lg-9">
                                {formcheckbox id='registrationProvider'|cat:$provider->getProviderName() group='config' cssClass='cmfcmfoauthmodule-registrationProvider'}
                            </div>
                        </div>
                        <div class="form-group">
                            {formlabel for='loginProvider'|cat:$provider->getProviderName() __text='Enable for login' cssClass=' col-lg-3 control-label'}
                            <div class="col-lg-9">
                                {formcheckbox id='loginProvider'|cat:$provider->getProviderName() group='config' cssClass='cmfcmfoauthmodule-loginProvider'}
                            </div>
                        </div>
                    </div>
                {/foreach}
            </div>

            <div class="form-group form-buttons">
            <div class="col-lg-offset-3 col-lg-9">
                {formbutton commandName='save' __text='Update configuration' class='btn btn-success'}
                {formbutton commandName='cancel' __text='Cancel' class='btn btn-default'}
            </div>
            </div>
        {/cmfcmfoauthmoduleFormFrame}
    {/form}
</div>
{include file='Admin/footer.tpl'}
