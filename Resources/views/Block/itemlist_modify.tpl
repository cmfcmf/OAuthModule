{* Purpose of this template: Edit block for generic item list *}
<div class="form-group">
    <label for="cmfcmfOAuthModuleObjectType" class="col-lg-3 control-label">{gt text='Object type'}:</label>
    <div class="col-lg-9">
        <select id="cmfcmfOAuthModuleObjectType" name="objecttype" size="1" class="form-control">
            <option value="user"{if $objectType eq 'user'} selected="selected"{/if}>{gt text='Users'}</option>
        </select>
        <span class="help-block">{gt text='If you change this please save the block once to reload the parameters below.'}</span>
    </div>
</div>

{if $properties ne null && is_array($properties)}
    {gt text='All' assign='lblDefault'}
    {nocache}
    {foreach key='propertyName' item='propertyId' from=$properties}
        <div class="form-group">
            {modapifunc modname='CmfcmfOAuthModule' type='category' func='hasMultipleSelection' ot=$objectType registry=$propertyName assign='hasMultiSelection'}
            {gt text='Category' assign='categoryLabel'}
            {assign var='categorySelectorId' value='catid'}
            {assign var='categorySelectorName' value='catid'}
            {assign var='categorySelectorSize' value='1'}
            {if $hasMultiSelection eq true}
                {gt text='Categories' assign='categoryLabel'}
                {assign var='categorySelectorName' value='catids'}
                {assign var='categorySelectorId' value='catids__'}
                {assign var='categorySelectorSize' value='8'}
            {/if}
            <label for="{$categorySelectorId}{$propertyName}" class="col-lg-3 control-label">{$categoryLabel}</label>
            <div class="col-lg-9">
                {selector_category name="`$categorySelectorName``$propertyName`" field='id' selectedValue=$catIds.$propertyName categoryRegistryModule='CmfcmfOAuthModule' categoryRegistryTable=$objectType categoryRegistryProperty=$propertyName defaultText=$lblDefault editLink=false multipleSize=$categorySelectorSize cssClass='form-control'}
                <span class="help-block">{gt text='This is an optional filter.'}</span>
            </div>
        </div>
    {/foreach}
    {/nocache}
{/if}

<div class="form-group">
    <label for="cmfcmfOAuthModuleSorting" class="col-lg-3 control-label">{gt text='Sorting'}:</label>
    <div class="col-lg-9">
        <select id="cmfcmfOAuthModuleSorting" name="sorting" class="form-control">
            <option value="random"{if $sorting eq 'random'} selected="selected"{/if}>{gt text='Random'}</option>
            <option value="newest"{if $sorting eq 'newest'} selected="selected"{/if}>{gt text='Newest'}</option>
            <option value="alpha"{if $sorting eq 'default' || ($sorting != 'random' && $sorting != 'newest')} selected="selected"{/if}>{gt text='Default'}</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label for="cmfcmfOAuthModuleAmount" class="col-lg-3 control-label">{gt text='Amount'}:</label>
    <div class="col-lg-9">
        <input type="text" id="cmfcmfOAuthModuleAmount" name="amount" maxlength="2" size="10" value="{$amount|default:"5"}" class="form-control" />
    </div>
</div>

<div class="form-group">
    <label for="cmfcmfOAuthModuleTemplate" class="col-lg-3 control-label">{gt text='Template'}:</label>
    <div class="col-lg-9">
        <select id="cmfcmfOAuthModuleTemplate" name="template" class="form-control">
            <option value="itemlist_display.tpl"{if $template eq 'itemlist_display.tpl'} selected="selected"{/if}>{gt text='Only item titles'}</option>
            <option value="itemlist_display_description.tpl"{if $template eq 'itemlist_display_description.tpl'} selected="selected"{/if}>{gt text='With description'}</option>
            <option value="custom"{if $template eq 'custom'} selected="selected"{/if}>{gt text='Custom template'}</option>
        </select>
    </div>
</div>

<div id="customTemplateArea" class="form-group hide">
    <label for="cmfcmfOAuthModuleCustomTemplate" class="col-lg-3 control-label">{gt text='Custom template'}:</label>
    <div class="col-lg-9">
        <input type="text" id="cmfcmfOAuthModuleCustomTemplate" name="customtemplate" size="40" maxlength="80" value="{$customTemplate|default:''}" class="form-control" />
        <span class="help-block">{gt text='Example'}: <em>itemlist_{$objecttype}_display.tpl</em></span>
    </div>
</div>

<div class="form-group">
    <label for="cmfcmfOAuthModuleFilter" class="col-lg-3 control-label">{gt text='Filter (expert option)'}:</label>
    <div class="col-lg-9">
        <input type="text" id="cmfcmfOAuthModuleFilter" name="filter" size="40" value="{$filterValue|default:''}" class="form-control" />
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
