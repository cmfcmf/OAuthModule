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
            {formsetinitialfocus inputId='suggestRegistrationOnFailedLogin'}
            {gt text='Main configuration' assign='tabTitle'}
            <fieldset>
                <legend>{$tabTitle}</legend>
            
                <p class="alert alert-info">{gt text='Here you can manage all basic settings for this application.'}</p>
            
                <div class="form-group">
                    {formlabel for='suggestRegistrationOnFailedLogin' __text='Suggest registration on failed login' cssClass=' col-lg-3 control-label'}
                    <div class="col-lg-9">
                        {formcheckbox id='suggestRegistrationOnFailedLogin' group='config'}
                    </div>
                </div>
            </fieldset>

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
