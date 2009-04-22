{*
** form form template
*}


   <div class="tab"><h2>{t}Question detail{/t}</h2></div>

		<fieldset id="question">
		
			
		<table class="bordered">
		
			<tr>
				<th>{t}title{/t}:</th>
				<td>
					<input type="text" name="data[title]" value="{$object.title|escape:'html'|escape:'quotes'}" id="titleBEObject" />
				</td>
			</tr>
		
			<tr>
				<th>{t}text{/t}:</th>
				<td>
					<textarea id="subtitle" style="width:380px; height:80px" class="shortdesc autogrowarea" name="data[description]">{$object.description|default:''|escape:'html'}</textarea>
				</td>
			</tr>
			
			<tr>
				<th>{t}type{/t}:</th>
				<td>
					<select name="data[question_type]" id="tipoID">
					{foreach from=$conf->questionTypes item="label" key="value"}
						<option value="{$value}"{if $value == $object.question_type|default:""} selected{/if}>{t}{$label}{/t}</option>
					{/foreach}
					</select>
				</td>
			</tr>


{literal}

<script>

$(document).ready(function(){	

	$(".add").click(function (){
		var row = $(this).parent().parent("tr");
		$(row).clone(true).insertAfter(row).addClass("newrow").find(".iteration").empty();
	});
	
	$(".remove").click(function (){
		//da fare if not:first
		var row = $(this).parent().parent("tr");
		$(row).remove();
 	});

	showAnswers();


});


$("#tipoID").change(function () {
	showAnswers();
});

function showAnswers() {
	$(".answers div").hide();
	$(".answers div :input").attr("disabled", "disabled");
	$(".answers div :checkbox").attr("disabled", "disabled");
	var kind = $("#tipoID option:selected").val();
	$("." + kind).show();
	$("." + kind + " :input").attr("disabled", "");
	$("." + kind + " :checkbox").attr("disabled", "");
}

</script>
{/literal}


			<tr>
				<th>{t}answers{/t}:</th>
				<td class="answers">
					
					<!--  -->
					
					<div style="display:none" class="multiple single_checkbox single_pulldown checkopen">
						<table>
						{if !empty($object.QuestionAnswer)}
							{foreach from=$object.QuestionAnswer item="answer" name="fca"}
								<tr>
									<td class="iteration" style="padding-left:0px;">{$smarty.foreach.fca.iteration}.&nbsp;
										<input type="hidden" name="data[QuestionAnswer][{$smarty.foreach.fca.index}][priority]" value="{$smarty.foreach.fca.iteration}" />
									</td>
									<td><textarea style="height:32px; width:240px !important;" name="data[QuestionAnswer][{$smarty.foreach.fca.index}][description]" class="autogrowarea">{$answer.description}</textarea></td>
									<td>
										&nbsp;&nbsp;<input type="checkbox" name="data[QuestionAnswer][{$smarty.foreach.fca.index}][correct]" value="1"{if $answer.correct == 1} checked="checked"{/if}>&nbsp;{t}correct{/t}&nbsp;&nbsp;&nbsp;
									</td>
									<td>
										<input type="button" class="add" title="{t}add{/t}" value="+" />
									</td>
									<td>
										<input type="button" class="remove" title="{t}remove{/t}" value="-" />
									</td>
									<td>
										
									</td>
								</tr>
							{/foreach}
						{else} 
							{section name="w" loop=5}
								<tr>
									<td class="iteration" style="padding-left:0px;">{$smarty.section.w.iteration}.&nbsp;
										<input type="hidden" name="data[QuestionAnswer][{$smarty.section.w.index}][priority]" value="{$smarty.section.w.iteration}" />
									</td>
									<td><textarea style="height:32px; width:240px !important;" name="data[QuestionAnswer][{$smarty.section.w.index}][description]" class="autogrowarea"></textarea></td>
									<td>
										&nbsp;&nbsp;<input type="checkbox" name="data[QuestionAnswer][{$smarty.section.w.index}][correct]" value="1">&nbsp;{t}correct{/t}&nbsp;&nbsp;&nbsp;
									</td>
									<td>
										<input type="button" class="add" title="{t}add{/t}" value="+" />
									</td>
									<td>
										<input type="button" class="remove" title="{t}remove{/t}" value="-" />
									</td>
									<td>
										
									</td>
								</tr>
							{/section}
						{/if}
						</table>
					</div>
					
					<!--  -->
					
					<div style="display:none" class="freetext">
						
						max characters: <input style="width:60px" type="text" name="data[max_chars]" value="{$object.max_chars|default:''}" />
						
						
					</div>
					
				</td>
			</tr>
				
			<tr>
		
				<th>{t}status{/t}:</th>
				<td colspan="4">
					{if $object.fixed}
						{t}This object is fixed - some data is readonly{/t}
						<input type="hidden" name="data[fixed]" value="1" />
						<input type="hidden" name="data[status]" value="{$object.status}" />
					{else}
						{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->status separator="&nbsp;"}
					{/if}
				</td>
		
			</tr>
		
		
			<tr>
				<th>{t}author{/t}:</th>
				<td>
					<input type="text" name="data[creator]" value="{$object.creator}" />
				</td>
			</tr>
		
		
			<tr>
				<th>{t}language{/t}:</th>
				<td>
				{assign var=object_lang value=$object.lang|default:$conf->defaultLang}
				<select name="data[lang]" id="main_lang">
					{foreach key=val item=label from=$conf->langOptions name=langfe}
					<option {if $val==$object_lang}selected="selected"{/if} value="{$val}">{$label}</option>
					{/foreach}
					{foreach key=val item=label from=$conf->langsIso name=langfe}
					<option {if $val==$object_lang}selected="selected"{/if} value="{$val}">{$label}</option>
					{/foreach}
				</select>
				</td>
			</tr>
		
		</table>
	
	</fieldset>

	{include file="../common_inc/form_file_list.tpl" containerId='multimediaContainer' collection="true" relation='attach' title='Multimedia'}

	{include file="../common_inc/form_tags.tpl"}
		
	{include file="../common_inc/form_translations.tpl"}

	{include file="../common_inc/form_advanced_properties.tpl" el=$object}
	

	
