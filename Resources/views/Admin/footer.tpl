{* purpose of this template: footer for admin area *}
{if !isset($smarty.get.theme) || $smarty.get.theme ne 'Printer'}
    <p class="text-center">
        Powered by <a href="http://modulestudio.de" title="Get the MOST out of Zikula!">ModuleStudio 0.6.1</a>
    </p>
    {adminfooter}
{/if}
