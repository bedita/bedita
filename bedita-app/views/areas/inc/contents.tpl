<script type="text/javascript">
<!--
var urlAddObjToAssLeafs = "{$html->url('/pages/loadObjectToAssoc')}/{$object.id|default:0}/leafs";
var pageUrl = "{$beurl->getUrl('object_type_id')}";
//-->
</script>

{$relcount = $objects|@count|default:0}
<div class="tab"><h2 {if $relcount == 0}class="empty"{/if}>{t}Contents{/t} {if $relcount > 0} &nbsp; <span class="relnumb">{$relcount}</span>{/if}</h2></div>

<fieldset id="areacontentC">

<div style="margin-top:10px;">

    <div id="areacontent">
        <table class="indexlist" style="width:100%; margin-bottom:10px;">
            <tbody class="disableSelection">
                <input type="hidden" name="contentsToRemove" id="contentsToRemove" value=""/>
                <tr id="noContents"{if !empty($objects)} style="display: none;"{/if}>
                    <td style="padding:0 10px 10px 10px">
                        <em>{t}no items{/t}</em>
                    </td>
                </tr>
                {$view->element('form_assoc_object',['objsRelated' => $objects])}
            </tbody>
        </table>
    </div>

{if $relcount > 5}      
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

    {include file="inc/tools_commands.tpl" type="all"}

</div>  

</fieldset>