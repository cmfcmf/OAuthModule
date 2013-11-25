{* Purpose of this template: Display mapped ids within an external context *}
{foreach item='mappedId' from=$items}
    <h3>{$mappedId->getTitleFromDisplayPattern()}</h3>
{/foreach}
