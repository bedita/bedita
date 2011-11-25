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


	<div style="text-align:right; padding-left:30px; padding-right:20px; float:right;"><a href="{$html->url('/')}areas/viewSection/branch:{$object.id}">
		{t}create{/t}  {t}new section{/t} {t}here{/t} &nbsp;
		<input type="button" value="GO" /></a>
	</div>
	
</div>




