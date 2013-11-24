'use strict';

var currentCmfcmfOAuthModuleEditor = null;
var currentCmfcmfOAuthModuleInput = null;

/**
 * Returns the attributes used for the popup window. 
 * @return {String}
 */
function getPopupAttributes()
{
    var pWidth, pHeight;

    pWidth = screen.width * 0.75;
    pHeight = screen.height * 0.66;
    return 'width=' + pWidth + ',height=' + pHeight + ',scrollbars,resizable';
}

/**
 * Open a popup window with the finder triggered by a Xinha button.
 */
function CmfcmfOAuthModuleFinderXinha(editor, oauthURL)
{
    var popupAttributes;

    // Save editor for access in selector window
    currentCmfcmfOAuthModuleEditor = editor;

    popupAttributes = getPopupAttributes();
    window.open(oauthURL, '', popupAttributes);
}

/**
 * Open a popup window with the finder triggered by a CKEditor button.
 */
function CmfcmfOAuthModuleFinderCKEditor(editor, oauthURL)
{
    // Save editor for access in selector window
    currentCmfcmfOAuthModuleEditor = editor;

    editor.popup(
        Zikula.Config.baseURL + Zikula.Config.entrypoint + '?module=CmfcmfOAuthModule&type=external&func=finder&editor=ckeditor',
        /*width*/ '80%', /*height*/ '70%',
        'location=no,menubar=no,toolbar=no,dependent=yes,minimizable=no,modal=yes,alwaysRaised=yes,resizable=yes,scrollbars=yes'
    );
}



var oauth = {};

oauth.finder = {};

oauth.finder.onLoad = function (baseId, selectedId)
{
    $$('div.categoryselector select').invoke('observe', 'change', oauth.finder.onParamChanged);
    $('cmfcmfOAuthModuleSort').observe('change', oauth.finder.onParamChanged);
    $('cmfcmfOAuthModuleSortDir').observe('change', oauth.finder.onParamChanged);
    $('cmfcmfOAuthModulePageSize').observe('change', oauth.finder.onParamChanged);
    $('cmfcmfOAuthModuleSearchGo').observe('click', oauth.finder.onParamChanged);
    $('cmfcmfOAuthModuleSearchGo').observe('keypress', oauth.finder.onParamChanged);
    $('cmfcmfOAuthModuleSubmit').addClassName('hide');
    $('cmfcmfOAuthModuleCancel').observe('click', oauth.finder.handleCancel);
};

oauth.finder.onParamChanged = function ()
{
    $('cmfcmfOAuthModuleSelectorForm').submit();
};

oauth.finder.handleCancel = function ()
{
    var editor, w;

    editor = $F('editorName');
    if (editor === 'xinha') {
        w = parent.window;
        window.close();
        w.focus();
    } else if (editor === 'tinymce') {
        oauthClosePopup();
    } else if (editor === 'ckeditor') {
        oauthClosePopup();
    } else {
        alert('Close Editor: ' + editor);
    }
};


function getPasteSnippet(mode, itemId)
{
    var itemUrl, itemTitle, itemDescription, pasteMode;

    itemUrl = $F('url' + itemId);
    itemTitle = $F('title' + itemId);
    itemDescription = $F('desc' + itemId);
    pasteMode = $F('cmfcmfOAuthModulePasteAs');

    if (pasteMode === '2' || pasteMode !== '1') {
        return itemId;
    }

    // return link to item
    if (mode === 'url') {
        // plugin mode
        return itemUrl;
    } else {
        // editor mode
        return '<a href="' + itemUrl + '" title="' + itemDescription + '">' + itemTitle + '</a>';
    }
}


// User clicks on "select item" button
oauth.finder.selectItem = function (itemId)
{
    var editor, html;

    editor = $F('editorName');
    if (editor === 'xinha') {
        if (window.opener.currentCmfcmfOAuthModuleEditor !== null) {
            html = getPasteSnippet('html', itemId);

            window.opener.currentCmfcmfOAuthModuleEditor.focusEditor();
            window.opener.currentCmfcmfOAuthModuleEditor.insertHTML(html);
        } else {
            html = getPasteSnippet('url', itemId);
            var currentInput = window.opener.currentCmfcmfOAuthModuleInput;

            if (currentInput.tagName === 'INPUT') {
                // Simply overwrite value of input elements
                currentInput.value = html;
            } else if (currentInput.tagName === 'TEXTAREA') {
                // Try to paste into textarea - technique depends on environment
                if (typeof document.selection !== 'undefined') {
                    // IE: Move focus to textarea (which fortunately keeps its current selection) and overwrite selection
                    currentInput.focus();
                    window.opener.document.selection.createRange().text = html;
                } else if (typeof currentInput.selectionStart !== 'undefined') {
                    // Firefox: Get start and end points of selection and create new value based on old value
                    var startPos = currentInput.selectionStart;
                    var endPos = currentInput.selectionEnd;
                    currentInput.value = currentInput.value.substring(0, startPos)
                                        + html
                                        + currentInput.value.substring(endPos, currentInput.value.length);
                } else {
                    // Others: just append to the current value
                    currentInput.value += html;
                }
            }
        }
    } else if (editor === 'tinymce') {
        html = getPasteSnippet('html', itemId);
        window.opener.tinyMCE.activeEditor.execCommand('mceInsertContent', false, html);
        // other tinymce commands: mceImage, mceInsertLink, mceReplaceContent, see http://www.tinymce.com/wiki.php/Command_identifiers
    } else if (editor === 'ckeditor') {
        /** to be done*/
    } else {
        alert('Insert into Editor: ' + editor);
    }
    oauthClosePopup();
};


function oauthClosePopup()
{
    window.opener.focus();
    window.close();
}




//=============================================================================
// CmfcmfOAuthModule item selector for Forms
//=============================================================================

oauth.itemSelector = {};
oauth.itemSelector.items = {};
oauth.itemSelector.baseId = 0;
oauth.itemSelector.selectedId = 0;

oauth.itemSelector.onLoad = function (baseId, selectedId)
{
    oauth.itemSelector.baseId = baseId;
    oauth.itemSelector.selectedId = selectedId;

    // required as a changed object type requires a new instance of the item selector plugin
    $('cmfcmfOAuthModuleObjectType').observe('change', oauth.itemSelector.onParamChanged);

    if ($(baseId + '_catidMain') != undefined) {
        $(baseId + '_catidMain').observe('change', oauth.itemSelector.onParamChanged);
    } else if ($(baseId + '_catidsMain') != undefined) {
        $(baseId + '_catidsMain').observe('change', oauth.itemSelector.onParamChanged);
    }
    $(baseId + 'Id').observe('change', oauth.itemSelector.onItemChanged);
    $(baseId + 'Sort').observe('change', oauth.itemSelector.onParamChanged);
    $(baseId + 'SortDir').observe('change', oauth.itemSelector.onParamChanged);
    $('cmfcmfOAuthModuleSearchGo').observe('click', oauth.itemSelector.onParamChanged);
    $('cmfcmfOAuthModuleSearchGo').observe('keypress', oauth.itemSelector.onParamChanged);

    oauth.itemSelector.getItemList();
};

oauth.itemSelector.onParamChanged = function ()
{
    $('ajax_indicator').removeClassName('hide');

    oauth.itemSelector.getItemList();
};

oauth.itemSelector.getItemList = function ()
{
    var baseId, pars, request;

    baseId = oauth.itemSelector.baseId;
    pars = 'ot=' + baseId + '&';
    if ($(baseId + '_catidMain') != undefined) {
        pars += 'catidMain=' + $F(baseId + '_catidMain') + '&';
    } else if ($(baseId + '_catidsMain') != undefined) {
        pars += 'catidsMain=' + $F(baseId + '_catidsMain') + '&';
    }
    pars += 'sort=' + $F(baseId + 'Sort') + '&' +
            'sortdir=' + $F(baseId + 'SortDir') + '&' +
            'searchterm=' + $F(baseId + 'SearchTerm');

    request = new Zikula.Ajax.Request('index.php?module=CmfcmfOAuthModule&type=ajax&func=getItemListFinder', {
        method: 'post',
        parameters: pars,
        onFailure: function(req) {
            Zikula.showajaxerror(req.getMessage());
        },
        onSuccess: function(req) {
            var baseId;
            baseId = oauth.itemSelector.baseId;
            oauth.itemSelector.items[baseId] = req.getData();
            $('ajax_indicator').addClassName('hide');
            oauth.itemSelector.updateItemDropdownEntries();
            oauth.itemSelector.updatePreview();
        }
    });
};

oauth.itemSelector.updateItemDropdownEntries = function ()
{
    var baseId, itemSelector, items, i, item;

    baseId = oauth.itemSelector.baseId;
    itemSelector = $(baseId + 'Id');
    itemSelector.length = 0;

    items = oauth.itemSelector.items[baseId];
    for (i = 0; i < items.length; ++i) {
        item = items[i];
        itemSelector.options[i] = new Option(item.title, item.id, false);
    }

    if (oauth.itemSelector.selectedId > 0) {
        $(baseId + 'Id').value = oauth.itemSelector.selectedId;
    }
};

oauth.itemSelector.updatePreview = function ()
{
    var baseId, items, selectedElement, i;

    baseId = oauth.itemSelector.baseId;
    items = oauth.itemSelector.items[baseId];

    $(baseId + 'PreviewContainer').addClassName('hide');

    if (items.length === 0) {
        return;
    }

    selectedElement = items[0];
    if (oauth.itemSelector.selectedId > 0) {
        for (var i = 0; i < items.length; ++i) {
            if (items[i].id === oauth.itemSelector.selectedId) {
                selectedElement = items[i];
                break;
            }
        }
    }

    if (selectedElement !== null) {
        $(baseId + 'PreviewContainer').update(window.atob(selectedElement.previewInfo))
                                      .removeClassName('hide');
    }
};

oauth.itemSelector.onItemChanged = function ()
{
    var baseId, itemSelector, preview;

    baseId = oauth.itemSelector.baseId;
    itemSelector = $(baseId + 'Id');
    preview = window.atob(oauth.itemSelector.items[baseId][itemSelector.selectedIndex].previewInfo);

    $(baseId + 'PreviewContainer').update(preview);
    oauth.itemSelector.selectedId = $F(baseId + 'Id');
};
