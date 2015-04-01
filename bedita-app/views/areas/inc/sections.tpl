{$relcount = $sections|@count|default:0}
<div class="tab"><h2 {if $relcount == 0}class="empty"{/if}>{t}Sections{/t} {if $relcount > 0} &nbsp; <span class="relnumb">{$relcount}</span>{/if}</h2></div>

<fieldset id="areasectionsC">
{if !empty($sections)}
    <div id="areasections">
        <table class="indexlist" style="width:100%; margin-bottom:10px;">
            <tbody>
    
            {$view->element('form_assoc_object', ['objsRelated' => $sections])}
                
            </tbody>
        </table>
    </div>

    {include file="inc/tools_commands.tpl" type="section"}

{else}
    <em style="display:block; margin: 10px;">{t}no sections{/t}</em>
{/if}
        
    

</fieldset>