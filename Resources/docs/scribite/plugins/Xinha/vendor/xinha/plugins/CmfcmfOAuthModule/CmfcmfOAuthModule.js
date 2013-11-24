// CmfcmfOAuthModule plugin for Xinha
// developed by Christian Flach
//
// requires CmfcmfOAuthModule module (https://www.github.com/cmfcmf/OAuth)
//
// Distributed under the same terms as xinha itself.
// This notice MUST stay intact for use (see license.txt).

'use strict';

function CmfcmfOAuthModule(editor) {
    var cfg, self;

    this.editor = editor;
    cfg = editor.config;
    self = this;

    cfg.registerButton({
        id       : 'CmfcmfOAuthModule',
        tooltip  : 'Insert CmfcmfOAuthModule object',
     // image    : _editor_url + 'plugins/CmfcmfOAuthModule/img/ed_CmfcmfOAuthModule.gif',
        image    : '/images/icons/extrasmall/favorites.png',
        textMode : false,
        action   : function (editor) {
            var url = Zikula.Config.baseURL + 'index.php'/*Zikula.Config.entrypoint*/ + '?module=CmfcmfOAuthModule&type=external&func=finder&editor=xinha';
            CmfcmfOAuthModuleFinderXinha(editor, url);
        }
    });
    cfg.addToolbarElement('CmfcmfOAuthModule', 'insertimage', 1);
}

CmfcmfOAuthModule._pluginInfo = {
    name          : 'CmfcmfOAuthModule for xinha',
    version       : '1.0.0',
    developer     : 'Christian Flach',
    developer_url : 'https://www.github.com/cmfcmf/OAuth',
    sponsor       : 'ModuleStudio 0.6.1',
    sponsor_url   : 'http://modulestudio.de',
    license       : 'htmlArea'
};
