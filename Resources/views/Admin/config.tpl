{* purpose of this template: module configuration *}
{include file='Admin/header.tpl'}
<div class="cmfcmfoauthmodule-config">
    {gt text='Settings' assign='templateTitle'}
    {pagesetvar name='title' value=$templateTitle}
    <h3>
        <span class="fa fa-wrench"></span>
        {$templateTitle}
    </h3>

    {form cssClass='form-horizontal' role='form'}
        {* add validation summary and a <div> element for styling the form *}
        {cmfcmfoauthmoduleFormFrame}
            {formsetinitialfocus inputId='loginProviderTwitter'}
            {formtabbedpanelset}
            {gt text='Login provider' assign='tabTitle'}
            {formtabbedpanel title=$tabTitle}
            <fieldset>
                <legend>{$tabTitle}</legend>
            
                <p class="alert alert-info">{gt text='Here you can select the providers to enable for login.'|nl2br}</p>
            
                <div class="form-group">
                    {formlabel for='loginProviderTwitter' __text='Login provider twitter' cssClass=' col-lg-3 control-label'}
                    <div class="col-lg-9">
                        {formcheckbox id='loginProviderTwitter' group='config'}
                    </div>
                </div>
                <div class="form-group">
                    {formlabel for='loginProviderGithub' __text='Login provider github' cssClass=' col-lg-3 control-label'}
                    <div class="col-lg-9">
                        {formcheckbox id='loginProviderGithub' group='config'}
                    </div>
                </div>
                <div class="form-group">
                    {formlabel for='loginProviderGoogle' __text='Login provider google' cssClass=' col-lg-3 control-label'}
                    <div class="col-lg-9">
                        {formcheckbox id='loginProviderGoogle' group='config'}
                    </div>
                </div>
            </fieldset>
            {/formtabbedpanel}
            {gt text='Registration provider' assign='tabTitle'}
            {formtabbedpanel title=$tabTitle}
            <fieldset>
                <legend>{$tabTitle}</legend>
            
                <p class="alert alert-info">{gt text='Here you can choose the providers to enable for registration.'|nl2br}</p>
            
                <div class="form-group">
                    {formlabel for='registrationProviderTwitter' __text='Registration provider twitter' cssClass=' col-lg-3 control-label'}
                    <div class="col-lg-9">
                        {formcheckbox id='registrationProviderTwitter' group='config'}
                    </div>
                </div>
                <div class="form-group">
                    {formlabel for='registrationProviderGithub' __text='Registration provider github' cssClass=' col-lg-3 control-label'}
                    <div class="col-lg-9">
                        {formcheckbox id='registrationProviderGithub' group='config'}
                    </div>
                </div>
                <div class="form-group">
                    {formlabel for='registrationProviderGoogle' __text='Registration provider google' cssClass=' col-lg-3 control-label'}
                    <div class="col-lg-9">
                        {formcheckbox id='registrationProviderGoogle' group='config'}
                    </div>
                </div>
            </fieldset>
            {/formtabbedpanel}
            {gt text='Twitter settings' assign='tabTitle'}
            {formtabbedpanel title=$tabTitle}
            <fieldset>
                <legend>{$tabTitle}</legend>
            
                <p class="alert alert-info">{gt text='You need to register an application at <a href="https://dev.twitter.com/apps">https://dev.twitter.com/apps</a> to use Twitter OAuth.
                
                After creating your application, please insert the "Consumer key" and the "Consumer secret" below.'|nl2br}</p>
            
                <div class="form-group">
                    {formlabel for='twitterConsumerKey' __text='Twitter consumer key' cssClass=' col-lg-3 control-label'}
                    <div class="col-lg-9">
                        {formtextinput id='twitterConsumerKey' group='config' maxLength=255 __title='Enter the twitter consumer key.' cssClass='form-control'}
                    </div>
                </div>
                <div class="form-group">
                    {formlabel for='twitterConsumerSecret' __text='Twitter consumer secret' cssClass=' col-lg-3 control-label'}
                    <div class="col-lg-9">
                        {formtextinput id='twitterConsumerSecret' group='config' maxLength=255 __title='Enter the twitter consumer secret.' cssClass='form-control'}
                    </div>
                </div>
            </fieldset>
            {/formtabbedpanel}
            {gt text='Github settings' assign='tabTitle'}
            {formtabbedpanel title=$tabTitle}
            <fieldset>
                <legend>{$tabTitle}</legend>
            
                <p class="alert alert-info">{gt text='You need to register an application at <a href="https://github.com/settings/applications/new">https://github.com/settings/applications/new</a> to use GitHub OAuth.
                
                After creating your application, please insert the "Consumer key" and the "Consumer secret" below.'|nl2br}</p>
            
                <div class="form-group">
                    {formlabel for='githubConsumerKey' __text='Github consumer key' cssClass=' col-lg-3 control-label'}
                    <div class="col-lg-9">
                        {formtextinput id='githubConsumerKey' group='config' maxLength=255 __title='Enter the github consumer key.' cssClass='form-control'}
                    </div>
                </div>
                <div class="form-group">
                    {formlabel for='githubConsumerSecret' __text='Github consumer secret' cssClass=' col-lg-3 control-label'}
                    <div class="col-lg-9">
                        {formtextinput id='githubConsumerSecret' group='config' maxLength=255 __title='Enter the github consumer secret.' cssClass='form-control'}
                    </div>
                </div>
            </fieldset>
            {/formtabbedpanel}
            {gt text='Google settings' assign='tabTitle'}
            {formtabbedpanel title=$tabTitle}
            <fieldset>
                <legend>{$tabTitle}</legend>
            
            
                <div class="form-group">
                    {formlabel for='googleConsumerKey' __text='Google consumer key' cssClass=' col-lg-3 control-label'}
                    <div class="col-lg-9">
                        {formtextinput id='googleConsumerKey' group='config' maxLength=255 __title='Enter the google consumer key.' cssClass='form-control'}
                    </div>
                </div>
                <div class="form-group">
                    {formlabel for='googleConsumerSecret' __text='Google consumer secret' cssClass=' col-lg-3 control-label'}
                    <div class="col-lg-9">
                        {formtextinput id='googleConsumerSecret' group='config' maxLength=255 __title='Enter the google consumer secret.' cssClass='form-control'}
                    </div>
                </div>
            </fieldset>
            {/formtabbedpanel}
            {/formtabbedpanelset}

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
