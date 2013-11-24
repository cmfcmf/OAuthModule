{* purpose of this template: header for user area *}
{pageaddvar name='javascript' value='prototype'}
{pageaddvar name='javascript' value='validation'}
{pageaddvar name='javascript' value='zikula'}
{pageaddvar name='javascript' value='livepipe'}
{pageaddvar name='javascript' value='zikula.ui'}
{pageaddvar name='javascript' value='zikula.imageviewer'}
{pageaddvar name='javascript' value='modules/Cmfcmf/OAuthModule/Resources/public/js/CmfcmfOAuthModule.js'}

{if !isset($smarty.get.theme) || $smarty.get.theme ne 'Printer'}
    <h2 class="userheader">{gt text='O auth' comment='This is the title of the header template'}</h2>
    {modulelinks modname='CmfcmfOAuthModule' type='user'}
{/if}
{insert name='getstatusmsg'}
