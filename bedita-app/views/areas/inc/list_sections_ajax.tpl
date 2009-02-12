{$javascript->link("jquery/jquery.disable.text.select", true)}

<script type="text/javascript">
<!--
{literal}
$(document).ready(function() {

	var startSecPriority = $("#areasections").find("input[name*='[priority]']:first").val();
	var urlS = ajaxSectionsUrl + "/{/literal}{$selectedId|default:''}{literal}";
	
	//$("#areasections").sortable ({
	$("#areasections table").find("tbody").sortable ({
		distance: 20,
		opacity:0.7,
		update: function() {
					$(this).fixItemsPriority(startSecPriority);
				}
	}).css("cursor","move");

	$("#sections_nav a").click(function() {
			
		$("#loading").show();
		$("#areasectionsC").load(urlS, 
				{
					page:$(this).attr("rel"),
					dim:$("#dimSectionsPage").val() 					
				}, function() {
			$("#loading").hide();
		});
		
	});
	
	$("#dimSectionsPage").change(function() {
		$("#loading").show();
		$("#areacontentC").load(urlS, {dim:$(this).val()}, function() {
			$("#loading").hide();
		});
	});

});

    $(function() {
        $('.disableSelection').disableTextSelect();
    });
	
{/literal}
//-->
</script>

<div style="min-height:100px; margin-top:10px;">
{if !empty($sections.items)}

	<div id="areasections">
	<table class="indexlist" style="width:100%; margin-bottom:10px;">
		<tbody class="disableSelection">
		{include file="inc/list_sections_for_section.tpl" objsRelated=$sections.items}
		</tbody>
	</table>
	</div>		
	
	<div id="sections_nav">
	{*
	{if $sections.toolbar.prev > 0}
		<a href="javascript:void(0);" rel="{$sections.toolbar.prev}" class="graced" style="font-size:3em">‹</a>
	{/if}
	{if $sections.toolbar.next > 0}
		<a href="javascript:void(0);" rel="{$sections.toolbar.next}" class="graced" style="font-size:3em">›</a>
	{/if}
	
	dim:
	<select name="dimSectionsPage" id="dimSectionsPage">
		<option value="5"{if $dimSec == 5} selected{/if}>5</option>
		<option value="10"{if $dimSec == 10} selected{/if}>10</option>
		<option value="20"{if $dimSec == 20} selected{/if}>20</option>
		<option value="50"{if $dimSec == 50} selected{/if}>50</option>
		<option value="1000000"{if $dimSec == 1000000} selected{/if}>tutti</option>
	</select>
	*}
	</div>

{else}
	&nbsp;&nbsp;<em>{t}no sections{/t}</em>
{/if}

</div>