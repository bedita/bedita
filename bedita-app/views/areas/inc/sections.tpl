{$beToolbar->init($sectionsToolbar,'sections-')}
{$relcount = $beToolbar->size()}

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
{if ($beToolbar->size() > 5)}      
    <div id="contents_nav_leafs" style="margin:0px 0 10px 0px; padding:10px 0px 10px 0px; overflow:hidden; border-bottom:1px solid gray" class="ignore">  
        <div style="padding-left:0px; float:left;">
        {t}show{/t}
        {assign var="allLabel" value=$tr->t("all", true)}
        {$beToolbar->changeDimSelect('selectTop', [], [5 => 5, 10 => 10, 20 => 20, 50 => 50, 100 => 100, 1000000 => $allLabel])} &nbsp;
        {t}item(s){/t} 
        </div>  
        {include file="inc/toolbar.tpl"}
    </div>
{/if}
{if !$isInsideHiddenBranch}
    {include file="inc/tools_commands.tpl" type="section"}
{/if}
</fieldset>