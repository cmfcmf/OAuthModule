{* Purpose of this template: Display users within an external context *}
{foreach item='user' from=$items}
    <h3>{$user->getTitleFromDisplayPattern()}</h3>
{/foreach}
