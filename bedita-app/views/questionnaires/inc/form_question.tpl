{*
** form question template
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
					
					{literal}
					
					<script type="text/javascript">
						function showAnswers() {
							$(".answers div").hide();
							$(".answers div :input").attr("disabled", "disabled");
							$(".answers div :checkbox").attr("disabled", "disabled");
							var kind = $("#tipoID option:selected").val();
							$("." + kind).show();
							$("." + kind + " :input").attr("disabled", "");
							$("." + kind + " :checkbox").attr("disabled", "");
						}
						
						$("#tipoID").change(function () {
							showAnswers();
							$('#preview_container_question').hide();
							$('#preview').val("EXAMPLE");
						});
						
						//preview functions
						$(document).ready(function(){	
							$("#preview").toggle( 
							function () 
							{ 
								var qtype = $('#tipoID OPTION:selected').val();
								$('#preview').toggleValue("CLOSE EXAMPLE","EXAMPLE");
								$('#preview_container_question DIV').not('.p_'+qtype).toggle();
								$('#preview_container_question').toggle();
							    $('#tipoID').attr("disabled", true); 
							}, 
							function () 
							{ 
								var qtype = $('#tipoID OPTION:selected').val();
								$('#preview').toggleValue("CLOSE EXAMPLE","EXAMPLE");
								$('#preview_container_question DIV').not('.p_'+qtype).toggle();
								$('#preview_container_question').toggle();
							    $('#tipoID').removeAttr("disabled"); 
							});
						});
					</script>
					{/literal}
					
					&nbsp; <input type="button" value="EXAMPLE" id="preview" class="BEbutton" />
				
				</td>
			</tr>
			
		<tr>
			<td colspan=2 style="padding:0px; border:0px">
			
			{include file="./inc/question_preview.tpl"}	
			
			</td>
		</tr>


		<tr>
			<td colspan=2 class="answers">
				
			{include file="./inc/answers_list.tpl"}	
				
			</td>
		</tr>
		
		<tr>
			<th>{t}edu level{/t}:</th>
			<td>
				<select name="data[edu_level]" id="eduLevel">
					<option value="">--</option>
				{foreach from=$conf->eduLevel item="label" key="value"}
					<option value="{$value}"{if $value == $object.edu_level|default:""} selected{/if}>{t}{$label}{/t}</option>
				{/foreach}
				</select>
			</td>
		</tr>

		<tr>
			<th>{t}difficulty{/t}:</th>
			<td>
				<select name="data[question_difficulty]" id="difficulty">
					<option value="">--</option>
				{foreach from=$conf->questionDifficulty item="label" key="value"}
					<option value="{$value}"{if $value == $object.question_difficulty|default:""} selected{/if}>{t}{$label}{/t}</option>
				{/foreach}
				</select>
			</td>
		</tr>
	
	</table>
	
	</fieldset>


   <div class="tab"><h2>{t}Question properties{/t}</h2></div>

	<fieldset id="properties">	
			
		<table class="bordered">

			<tr>
		
				<th>{t}status{/t}:</th>
				<td colspan="4">
					{if $object.fixed}
						{t}This object is fixed - some data is readonly{/t}
						<input type="hidden" name="data[fixed]" value="1" />
						<input type="hidden" name="data[status]" value="{$object.status}" />
					{else}

						{html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->defaultStatus separator="&nbsp;"}

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

	{*include file="../common_inc/form_file_list.tpl" containerId='multimediaContainer' collection="true" relation='attach' title='Multimedia'*}

	{include file="../common_inc/form_tags.tpl"}
		
	{include file="../common_inc/form_translations.tpl"}

	{include file="../common_inc/form_advanced_properties.tpl" el=$object}
	

	
