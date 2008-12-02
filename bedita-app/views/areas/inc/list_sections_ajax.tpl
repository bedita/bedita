<script type="text/javascript">
<!--
{literal}
$(document).ready(function() {

	var startSecPriority = $("#areasections").find("input[name*='[priority]']:first").val();
	var urlS = ajaxSectionsUrl + "/{/literal}{$selectedId|default:''}{literal}";

	$("#areasections").sortable ({
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
{/literal}
//-->
</script>

<div style="min-height:100px; margin-top:10px;">
{if !empty($sections.items)}

	<ul id="areasections" class="bordered">
		{foreach from=$sections.items item=s}
		<li class="itemBox obj {$s.status}">
			<input type="hidden" class="id" 	name="reorder[{$s.id}][id]" value="{$s.id}" />
			<input type="text" class="priority"  name="reorder[{$s.id}][priority]" value="{$s.priority}" size="3" maxlength="3"/>
			<span class="listrecent areas" style="margin-left:0px">&nbsp;&nbsp;</span>
			<a title="{$s.created}" href="{$html->url('/')}areas/index/{$s.id}">{$s.title|truncate:"70":"…":true}</a>
			
			<div style="margin-top:-20px; float:right;">
				{$s.lang}
			</div>
			
		</li>
		{/foreach}
	</ul>		
	
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