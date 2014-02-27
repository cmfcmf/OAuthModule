{* purpose of this template: module configuration *}
{include file='Admin/header.tpl'}
{pageaddvar name='javascript' value='modules/Cmfcmf/OAuthModule/Resources/public/js/CmfcmfOAuthModule.Admin.Config.js'}
{pageaddvarblock}
<style type="text/css">
    .cmfcmfoauthmodule-config .cmfcmfoauthmodule-passwordToggle {
        cursor: pointer;
    }
    .cmfcmfoauthmodule-config .nav.nav-tabs {
        margin-bottom: 15px;
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
                <li class="active"><a href="#general" data-toggle="tab">{gt text='General settings'}</a></li>
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
                    <div class="form-group">
                        {formlabel for='useMaximumInformationForRegistration' __text='Request as much information from the user as possbile during registration.' cssClass=' col-lg-3 control-label'}
                        <div class="col-lg-9">
                            {formcheckbox id='useMaximumInformationForRegistration' group='config'}
                            <div class="alert alert-info">{gt text="Why should you disable this feature? Users might be feared, because it often isn't possible to specify which data we want exactly. Basically we are interested in the user's email address, his nickname and his language. But for most of the providers you cannot request only those, but potentially get much more information than needed. The user now may ask why your site want's that much information. But why should you enable this? For some providers, this really means a two-click-only account registration on your site, which is very fast and easy for new users."}</div>
                        </div>
                    </div>
                    <div class="form-group">
                        {formlabel for='curlUseOwnCert' __text='Path to a curl certificate, if your server has not one by default' cssClass=' col-lg-3 control-label'}
                        <div class="col-lg-9">
                            {formtextinput id='curlUseOwnCert' group='config' maxLength=1000 cssClass="form-control"}
                            <div class="alert alert-info">
                                {gt text='Leave this empty, if you do not receive an expception when using Twitter or Google. If you do so, the problem might be related to missing certificates. Quote from the offcial website of the cURL library:'}<br />
                                <blockquote>
                                    <i>
                                        {gt text="Until 7.18.0, curl bundled a severely outdated ca bundle file that was installed by default. These days, the curl archives include no ca certs at all. You need to get them elsewhere."}
                                    </i>
                                    <small>
                                        <cite title="http://curl.haxx.se/docs/sslcerts.html">
                                            <a href="http://curl.haxx.se/docs/sslcerts.html" title="{gt text='Source'}">http://curl.haxx.se/docs/sslcerts.html</a>
                                        </cite>
                                    </small>
                                </blockquote>
                                {gt text='By default, your server hoster should take care of this issue. If the issue remains, enter a valid file path starting at SERVER root (not Zikula root) pointing to a valida certificates file. If such a file is not present on your server, you can either download one from the url below or use the one bundled with this module (which might be outdated). The path to the certificate file bundled with this module is as follows: %s' tag1=$pathToBundledCurlCertificate}<br />
                                <a href="http://curl.haxx.se/docs/caextract.html">http://curl.haxx.se/docs/caextract.html</a>
                            </div>
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
