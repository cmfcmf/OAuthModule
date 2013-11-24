{* Purpose of this template: Display a popup selector of users for scribite integration *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{lang}" lang="{lang}">
<head>
    <title>{gt text='Search and select user'}</title>
    <link type="text/css" rel="stylesheet" href="{$baseurl}style/core.css" />
    <link type="text/css" rel="stylesheet" href="{$baseurl}modules/Cmfcmf/OAuthModule/Resources/public/css/style.css" />
    <link type="text/css" rel="stylesheet" href="{$baseurl}modules/Cmfcmf/OAuthModule/Resources/public/css/finder.css" />
    {assign var='ourEntry' value=$modvars.ZConfig.entrypoint}
    <script type="text/javascript">/* <![CDATA[ */
        if (typeof(Zikula) == 'undefined') {var Zikula = {};}
        Zikula.Config = {'entrypoint': '{{$ourEntry|default:'index.php'}}', 'baseURL': '{{$baseurl}}'}; /* ]]> */</script>
        <script type="text/javascript" src="{$baseurl}javascript/ajax/proto_scriptaculous.combined.min.js"></script>
        <script type="text/javascript" src="{$baseurl}javascript/helpers/Zikula.js"></script>
        <script type="text/javascript" src="{$baseurl}javascript/livepipe/livepipe.combined.min.js"></script>
        <script type="text/javascript" src="{$baseurl}javascript/helpers/Zikula.UI.js"></script>
        <script type="text/javascript" src="{$baseurl}javascript/helpers/Zikula.ImageViewer.js"></script>
    <script type="text/javascript" src="{$baseurl}modules/Cmfcmf/OAuthModule/Resources/public/js/CmfcmfOAuthModule_finder.js"></script>
</head>
<body>
    <form action="{$ourEntry|default:'index.php'}" id="cmfcmfOAuthModuleSelectorForm" method="get" class="form-horizontal" role="form">
    <div>
        <input type="hidden" name="module" value="CmfcmfOAuthModule" />
        <input type="hidden" name="type" value="external" />
        <input type="hidden" name="func" value="finder" />
        <input type="hidden" name="objectType" value="{$objectType}" />
        <input type="hidden" name="editor" id="editorName" value="{$editorName}" />

        <fieldset>
            <legend>{gt text='Search and select user'}</legend>

            <div class="form-group">
                <label for="cmfcmfOAuthModulePasteAs" class="col-lg-3 control-label">{gt text='Paste as'}:</label>
                <div class="col-lg-9">
                    <select id="cmfcmfOAuthModulePasteAs" name="pasteas" class="form-control">
                        <option value="1">{gt text='Link to the user'}</option>
                        <option value="2">{gt text='ID of user'}</option>
                    </select>
                </div>
            </div>
            <br />

            <div class="form-group">
                <label for="cmfcmfOAuthModuleObjectId" class="col-lg-3 control-label">{gt text='User'}:</label>
                <div class="col-lg-9">
                    <div id="cmfcmfoauthmoduleItemContainer">
                        <ul>
                        {foreach item='user' from=$items}
                            <li>
                                <a href="#" onclick="oauth.finder.selectItem({$user.id})" onkeypress="oauth.finder.selectItem({$user.id})">{$user->getTitleFromDisplayPattern()}</a>
                                <input type="hidden" id="url{$user.id}" value="{modurl modname='CmfcmfOAuthModule' type='user' func='display' ot='user' id=$user.id fqurl=true}" />
                                <input type="hidden" id="title{$user.id}" value="{$user->getTitleFromDisplayPattern()|replace:"\"":""}" />
                                <input type="hidden" id="desc{$user.id}" value="{capture assign='description'}{if $user.claimedId ne ''}{$user.claimedId}{/if}
                                {/capture}{$description|strip_tags|replace:"\"":""}" />
                            </li>
                        {foreachelse}
                            <li>{gt text='No entries found.'}</li>
                        {/foreach}
                        </ul>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="cmfcmfOAuthModuleSort" class="col-lg-3 control-label">{gt text='Sort by'}:</label>
                <div class="col-lg-9">
                    <select id="cmfcmfOAuthModuleSort" name="sort" style="width: 150px" class="pull-left" style="margin-right: 10px">
                    <option value="id"{if $sort eq 'id'} selected="selected"{/if}>{gt text='Id'}</option>
                    <option value="workflowState"{if $sort eq 'workflowState'} selected="selected"{/if}>{gt text='Workflow state'}</option>
                    <option value="userId"{if $sort eq 'userId'} selected="selected"{/if}>{gt text='User id'}</option>
                    <option value="claimedId"{if $sort eq 'claimedId'} selected="selected"{/if}>{gt text='Claimed id'}</option>
                    <option value="provider"{if $sort eq 'provider'} selected="selected"{/if}>{gt text='Provider'}</option>
                    <option value="createdDate"{if $sort eq 'createdDate'} selected="selected"{/if}>{gt text='Creation date'}</option>
                    <option value="createdUserId"{if $sort eq 'createdUserId'} selected="selected"{/if}>{gt text='Creator'}</option>
                    <option value="updatedDate"{if $sort eq 'updatedDate'} selected="selected"{/if}>{gt text='Update date'}</option>
                    </select>
                    <select id="cmfcmfOAuthModuleSortDir" name="sortdir" style="width: 100px" class="form-control">
                        <option value="asc"{if $sortdir eq 'asc'} selected="selected"{/if}>{gt text='ascending'}</option>
                        <option value="desc"{if $sortdir eq 'desc'} selected="selected"{/if}>{gt text='descending'}</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="cmfcmfOAuthModulePageSize" class="col-lg-3 control-label">{gt text='Page size'}:</label>
                <div class="col-lg-9">
                    <select id="cmfcmfOAuthModulePageSize" name="num" style="width: 50px; text-align: right" class="form-control">
                        <option value="5"{if $pager.itemsperpage eq 5} selected="selected"{/if}>5</option>
                        <option value="10"{if $pager.itemsperpage eq 10} selected="selected"{/if}>10</option>
                        <option value="15"{if $pager.itemsperpage eq 15} selected="selected"{/if}>15</option>
                        <option value="20"{if $pager.itemsperpage eq 20} selected="selected"{/if}>20</option>
                        <option value="30"{if $pager.itemsperpage eq 30} selected="selected"{/if}>30</option>
                        <option value="50"{if $pager.itemsperpage eq 50} selected="selected"{/if}>50</option>
                        <option value="100"{if $pager.itemsperpage eq 100} selected="selected"{/if}>100</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="cmfcmfOAuthModuleSearchTerm" class="col-lg-3 control-label">{gt text='Search for'}:</label>
            <div class="col-lg-9">
                    <input type="text" id="cmfcmfOAuthModuleSearchTerm" name="searchterm" style="width: 150px" class="form-control pull-left" style="margin-right: 10px" />
                    <input type="button" id="cmfcmfOAuthModuleSearchGo" name="gosearch" value="{gt text='Filter'}" style="width: 80px" class="btn btn-default" />
            </div>
            </div>

            <div style="margin-left: 6em">
                {pager display='page' rowcount=$pager.numitems limit=$pager.itemsperpage posvar='pos' template='pagercss.tpl' maxpages='10'}
            </div>
            <input type="submit" id="cmfcmfOAuthModuleSubmit" name="submitButton" value="{gt text='Change selection'}" class="btn btn-success" />
            <input type="button" id="cmfcmfOAuthModuleCancel" name="cancelButton" value="{gt text='Cancel'}" class="btn btn-default" />
            <br />
        </fieldset>
    </div>
    </form>

    <script type="text/javascript">
    /* <![CDATA[ */
        document.observe('dom:loaded', function() {
            oauth.finder.onLoad();
        });
    /* ]]> */
    </script>

    {*
    <div class="cmfcmfoauthmodule-finderform">
        <fieldset>
            {modfunc modname='CmfcmfOAuthModule' type='admin' func='edit'}
        </fieldset>
    </div>
    *}
</body>
</html>
