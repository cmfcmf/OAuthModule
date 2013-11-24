{* Purpose of this template: edit view of generic item list content type *}

<div class="form-group">
    {gt text='Object type' domain='module_cmfcmfoauthmodule' assign='objectTypeSelectorLabel'}
    {formlabel for='cmfcmfOAuthModuleObjectType' text=$objectTypeSelectorLabel cssClass='col-lg-3 control-label'}
    <div class="col-lg-9">
        {cmfcmfoauthmoduleObjectTypeSelector assign='allObjectTypes'}
        {formdropdownlist id='cmfcmfOAuthModuleOjectType' dataField='objectType' group='data' mandatory=true items=$allObjectTypes cssClass='form-control'}
        <span class="help-block">{gt text='If you change this please save the element once to reload the parameters below.' domain='module_cmfcmfoauthmodule'}</span>
    </div>
</div>

{formvolatile}
{if $properties ne null && is_array($properties)}
    {nocache}
    {foreach key='registryId' item='registryCid' from=$registries}
        {assign var='propName' value=''}
        {foreach key='propertyName' item='propertyId' from=$properties}
            {if $propertyId eq $registryId}
                {assign var='propName' value=$propertyName}
            {/if}
        {/foreach}
        <div class="form-group">
            {modapifunc modname='CmfcmfOAuthModule' type='category' func='hasMultipleSelection' ot=$objectType registry=$propertyName assign='hasMultiSelection'}
            {gt text='Category' domain='module_cmfcmfoauthmodule' assign='categorySelectorLabel'}
            {assign var='selectionMode' value='single'}
            {if $hasMultiSelection eq true}
                {gt text='Categories' domain='module_cmfcmfoauthmodule' assign='categorySelectorLabel'}
                {assign var='selectionMode' value='multiple'}
            {/if}
            {formlabel for="cmfcmfOAuthModuleCatIds`$propertyName`" text=$categorySelectorLabel cssClass='col-lg-3 control-label'}
            <div class="col-lg-9">
                {formdropdownlist id="cmfcmfOAuthModuleCatIds`$propName`" items=$categories.$propName dataField="catids`$propName`" group='data' selectionMode=$selectionMode cssClass='form-control'}
                <span class="help-block">{gt text='This is an optional filter.' domain='module_cmfcmfoauthmodule'}</span>
            </div>
        </div>
    {/foreach}
    {/nocache}
{/if}
{/formvolatile}

<div class="form-group">
    {gt text='Sorting' domain='module_cmfcmfoauthmodule' assign='sortingLabel'}
    {formlabel text=$sortingLabel cssClass='col-lg-3 control-label'}
    <div class="col-lg-9">
        {formradiobutton id='cmfcmfOAuthModuleSortRandom' value='random' dataField='sorting' group='data' mandatory=true}
        {gt text='Random' domain='module_cmfcmfoauthmodule' assign='sortingRandomLabel'}
        {formlabel for='cmfcmfOAuthModuleSortRandom' text=$sortingRandomLabel}
        {formradiobutton id='cmfcmfOAuthModuleSortNewest' value='newest' dataField='sorting' group='data' mandatory=true}
        {gt text='Newest' domain='module_cmfcmfoauthmodule' assign='sortingNewestLabel'}
        {formlabel for='cmfcmfOAuthModuleSortNewest' text=$sortingNewestLabel}
        {formradiobutton id='cmfcmfOAuthModuleSortDefault' value='default' dataField='sorting' group='data' mandatory=true}
        {gt text='Default' domain='module_cmfcmfoauthmodule' assign='sortingDefaultLabel'}
        {formlabel for='cmfcmfOAuthModuleSortDefault' text=$sortingDefaultLabel}
    </div>
</div>

<div class="form-group">
    {gt text='Amount' domain='module_cmfcmfoauthmodule' assign='amountLabel'}
    {formlabel for='cmfcmfOAuthModuleAmount' text=$amountLabel cssClass='col-lg-3 control-label'}
    <div class="col-lg-9">
        {formintinput id='cmfcmfOAuthModuleAmount' dataField='amount' group='data' mandatory=true maxLength=2}
    </div>
</div>

<div class="form-group">
    {gt text='Template' domain='module_cmfcmfoauthmodule' assign='templateLabel'}
    {formlabel for='cmfcmfOAuthModuleTemplate' text=$templateLabel cssClass='col-lg-3 control-label'}
    <div class="col-lg-9">
        {cmfcmfoauthmoduleTemplateSelector assign='allTemplates'}
        {formdropdownlist id='cmfcmfOAuthModuleTemplate' dataField='template' group='data' mandatory=true items=$allTemplates cssClass='form-control'}
    </div>
</div>

<div id="customTemplateArea" class="form-group hide">
    {gt text='Custom template' domain='module_cmfcmfoauthmodule' assign='customTemplateLabel'}
    {formlabel for='cmfcmfOAuthModuleCustomTemplate' text=$customTemplateLabel cssClass='col-lg-3 control-label'}
    <div class="col-lg-9">
        {formtextinput id='cmfcmfOAuthModuleCustomTemplate' dataField='customTemplate' group='data' mandatory=false maxLength=80 cssClass='form-control'}
        <span class="help-block">{gt text='Example' domain='module_cmfcmfoauthmodule'}: <em>itemlist_[objecttype]_display.tpl</em></span>
    </div>
</div>

<div class="form-group">
    {gt text='Filter (expert option)' domain='module_cmfcmfoauthmodule' assign='filterLabel'}
    {formlabel for='cmfcmfOAuthModuleFilter' text=$filterLabel cssClass='col-lg-3 control-label'}
    <div class="col-lg-9">
        {formtextinput id='cmfcmfOAuthModuleFilter' dataField='filter' group='data' mandatory=false maxLength=255 cssClass='form-control'}
        <span class="help-block">
            <a class="fa fa-filter" data-toggle="modal" data-target="#filterSyntaxModal">{gt text='Show syntax examples'}</a>
        </span>
    </div>
</div>

{include file='include_filterSyntaxDialog.tpl'}

{pageaddvar name='javascript' value='prototype'}
<script type="text/javascript">
/* <![CDATA[ */
    function oauthToggleCustomTemplate() {
        if ($F('cmfcmfOAuthModuleTemplate') == 'custom') {
            $('customTemplateArea').removeClassName('hide');
        } else {
            $('customTemplateArea').addClassName('hide');
        }
    }

    document.observe('dom:loaded', function() {
        oauthToggleCustomTemplate();
        $('cmfcmfOAuthModuleTemplate').observe('change', function(e) {
            oauthToggleCustomTemplate();
        });
    });
/* ]]> */
</script>
