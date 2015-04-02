{$relcount = $sections|@count|default:0}
<div class="tab"><h2 {if $relcount == 0}class="empty"{/if}>{t}Sections{/t} {if $relcount > 0} &nbsp; <span class="relnumb">{$relcount}</span>{/if}</h2></div>

<fieldset id="areasectionsC">
    <div id="areasections">
        <table class="indexlist" style="width:100%; margin-bottom:10px;">
            <tbody>
            {if !empty($sections)}    
                {$view->element('form_assoc_object', ['objsRelated' => $sections, 'removeButton' => false ])}
            {else}
                <em style="display:block; margin: 10px;">{t}no sections{/t}</em>
            {/if}
            </tbody>
        </table>
    </div>
    {include file="inc/tools_commands.tpl" type="section"}
</fieldset>