CKEDITOR.plugins.add('CmfcmfOAuthModule', {
    requires: 'popup',
    lang: 'en,nl,de',
    init: function (editor) {
        editor.addCommand('insertCmfcmfOAuthModule', {
            exec: function (editor) {
                var url = Zikula.Config.baseURL + Zikula.Config.entrypoint + '?module=CmfcmfOAuthModule&type=external&func=finder&editor=ckeditor';
                // call method in CmfcmfOAuthModule_Finder.js and also give current editor
                CmfcmfOAuthModuleFinderCKEditor(editor, url);
            }
        });
        editor.ui.addButton('cmfcmfoauthmodule', {
            label: 'Insert CmfcmfOAuthModule object',
            command: 'insertCmfcmfOAuthModule',
         // icon: this.path + 'images/ed_cmfcmfoauthmodule.png'
            icon: '/images/icons/extrasmall/favorites.png'
        });
    }
});
