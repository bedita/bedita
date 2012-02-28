{$html->script("jquery/jquery.disable.text.select", true)}

<script type="text/javascript">
<!--
{literal}
$(document).ready(function() {

	var startSecPriority = $("#areasections").find("input[name*='[priority]']:first").val();
	
	//$("#areasections").sortable ({
	$("#areasections table").find("tbody").sortable ({
		distance: 20,
		opacity:0.7,
		update: function() {
					$(this).fixItemsPriority(startSecPriority);
				}
	}).css("cursor","move");

});

    $(function() {
        $('.disableSelection').disableTextSelect();
    });
	
{/literal}
//-->
</script>

<div style="min-height:100px; margin-top:10px;">
{if !empty($sections)}

	<div id="areasections">
	<table class="indexlist" style="width:100%; margin-bottom:10px;">
		<tbody class="disableSelection">
		
		{* children: sections *}
		{foreach from=$sections item="s"}
		
			<tr class="obj {$s.status}">
				
				<td class="checklist">
					
					{if ($s.menu == 1)}
					<img title="{t}hidden from menu and canonical path{/t}" src="{$html->webroot}img/iconHidden.png" style="height:30px; vertical-align:top;">
					{/if}
					
					{if !empty($s.start_date) && ($s.start_date|date_format:"%Y%m%d") > ($smarty.now|date_format:"%Y%m%d")}
					
						<img title="{t}object scheduled in the future{/t}" src="{$html->webroot}img/iconFuture.png" style="height:28px; vertical-align:top;">
					
					{elseif !empty($s.end_date) && ($s.end_date|date_format:"%Y%m%d") < ($smarty.now|date_format:"%Y%m%d")}
					
						<img title="{t}object expired{/t}" src="{$html->webroot}img/iconPast.png" style="height:28px; vertical-align:top;">
					
					{elseif (!empty($s.start_date) && (($s.start_date|date_format:"%Y%m%d") == ($smarty.now|date_format:"%Y%m%d"))) or ( !empty($s.end_date) && (($s.end_date|date_format:"%Y%m%d") == ($smarty.now|date_format:"%Y%m%d")))}
					
						<img title="{t}object scheduled today{/t}" src="{$html->webroot}img/iconToday.png" style="height:28px; vertical-align:top;">
		
					{/if}
					
					{if !empty($s.num_of_permission)}
						<img title="{t}permissions set{/t}" src="{$html->webroot}img/iconLocked.png" style="height:28px; vertical-align:top;">
					{/if}
					
					{if (empty($s.fixed))}
						<input style="margin-top:8px;" type="checkbox" name="objects_selected[]" class="objectCheck" title="{$s.id}" value="{$s.id}" />
					{else}
						<img title="{t}fixed object{/t}" src="{$html->webroot}img/iconFixed.png" style="margin-top:8px; height:12px;" />
					{/if}
				</td>
				
				<td style="width:25px">
					<input type="hidden" class="id" 	name="reorder[{$s.id}][id]" value="{$s.id}" />
					<input type="text" class="priority"	name="reorder[{$s.id}][priority]" value="{$s.priority|default:""}" 
					style="width:25px"
					size="3" maxlength="3"/>
				</td>
				<td style="padding:0px; padding-top:7px; width:10px"><span class="listrecent areas" style="margin:0px"></span></td>
				<td>
					{$s.title|default:'<i>[no title]</i>'|truncate:"64":"…":true}
					<div class="description" style="width:auto" id="desc_{$s.id}">
						{$s.description|strip_tags} / id:{$s.id} / nickname: {$s.nickname}
					</div>
				</td>

			<td>
					<a href="javascript:void(0)" onclick="$('#desc_{$s.id}').slideToggle(); $('.plusminus',this).toggleText('+','-')">
						<span class="plusminus">+</span></a>	
				</td>
				<td style="white-space:nowrap">
					{$s.modified|date_format:$conf->dateTimePattern}
				</td>
				<td>
					{$s.status}
				</td>

				<td>
					{$s.lang}
				</td>
				
				<td>{if $s.num_of_editor_note|default:''}<img src="{$html->webroot}img/iconNotes.gif" alt="notes" />{/if}</td>
				
				<td class="commands" style="white-space:nowrap">

					<input type="button" class="BEbutton golink" onClick="window.location.href = ($(this).attr('href'));" 
					href="{$html->url('/')}areas/view/{$s.id}" name="details" value="››" />

					{if !empty($s.fixed)}
						<img title="{t}fixed object{/t}" src="{$html->webroot}img/iconFixed.png" style="margin-left:10px; height:12px;" />
					{/if}

				</td>
			</tr>
		{/foreach}	
			
		</tbody>
	</table>
	</div>		
	
{else}
	<em style="padding:20px;">{t}no sections{/t}</em>
{/if}


	
	{include file="inc/tools_commands.tpl" type="section"}
	
	{include file="inc/bulk_actions.tpl" type="section"}	
		
</div>




