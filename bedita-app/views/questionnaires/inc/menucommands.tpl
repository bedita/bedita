{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}


<script type="text/javascript">
<!--
{literal}
$(document).ready(function(){

	
	$(".on").parents().show();
	

});


{/literal}

//-->
</script>


<div class="secondacolonna {if !empty($fixed)}fixed{/if}">

	{if !empty($method) && $method != "index"}
		{assign var="back" value=$session->read("backFromView")}
	{else}
		{assign_concat var="back" 0=$html->url('/') 1=$currentModule.path}
	{/if}

	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 

	{if !empty($method) && $method == "viewResults"}
	
	
	{elseif !empty($method) && $method != "index" && $method != "indexQuestions"}
	
	<div class="insidecol">

		<input class="bemaincommands" type="button" value=" {t}save{/t} " name="save" id="saveBEObject" />
		<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" id="cloneBEObject" />
		<input class="bemaincommands" type="button" value="{t}delete{/t}" name="delete" id="delBEObject" />
		
		{if !empty($object)  && $method == "view"}
		<hr />
		<input class="bemaincommands" onClick="window.location.href='{$html->url('index_sessions_results/')}'" type="button" value="{t}view results{/t}" />
		{/if}
	
		{include file="../common_inc/prevnext.tpl"}

		
	</div>
	
	{elseif $method == "indexQuestions"}
		
		{assign var="currentUrl" value=$beurl->getUrl()}

		<ul class="menuleft insidecol">
			<li><a href="javascript:void(0)" onClick="$('#groups').slideToggle();">{t}Select by category{/t}</a></li>
				
				<ul class="subchoice" id="groups">
					<li><a href="">matematica</a></li>
					<li><a href="">fisica</a></li>
					<li><a href="">geologia</a></li>
					<li><a href="">chimica</a></li>
					<li><a href="">francese</a></li>
					<li><a href="">italiano</a></li>
				</ul>
		</ul>

		<ul class="menuleft insidecol">
			<li><a href="javascript:void(0)" onClick="$('#types').slideToggle();">{t}Select by type{/t}</a></li>
				
				<ul class="subchoice" id="types">
					{foreach from=$conf->questionTypes item="label" key="value"}
					<li {if $html->params.named.question_type|default:'' == $value}class="on"{/if}>
						<a href="{$currentUrl}/question_type:{$value}">{$label}</a></li>
					{/foreach}
					<li class="all"><a href="{$beurl->getUrl('question_type')}">{t}all{/t}</a></li>
				</ul>
		</ul>

		<ul class="menuleft insidecol">
			<li><a href="javascript:void(0)" onClick="$('#difficulty').slideToggle();">{t}Select by difficulty{/t}</a></li>
				
				<ul class="subchoice" id="difficulty">
					{foreach from=$conf->questionDifficulty item="label" key="value"}
					<li {if $html->params.named.question_difficulty|default:'' == $value}class="on"{/if}>
						<a href="{$currentUrl}/question_difficulty:{$value}">{$label}</a></li>
					{/foreach}
					<li class="all"><a href="{$beurl->getUrl('question_difficulty')}">{t}all{/t}</a></li>
				</ul>
		</ul>
		
		<ul class="menuleft insidecol">
			<li><a href="javascript:void(0)" onClick="$('#eduLevel').slideToggle();">{t}Select by eduLevel{/t}</a></li>
				
				<ul class="subchoice" id="eduLevel">
					{foreach from=$conf->eduLevel item="label" key="value"}
					<li {if $html->params.named.edu_level|default:'' == $value}class="on"{/if}>
						<a href="{$currentUrl}/edu_level:{$value}">{$label}</a></li>
					{/foreach}
					<li class="all"><a href="{$beurl->getUrl('edu_level')}">{t}all{/t}</a></li>
				</ul>
		</ul>
		
		
			{literal}
			<script type="text/javascript">
			<!--
			$(document).ready(function(){
				
				var showTagsFirst = false;
				var showTags = false;
				$("#callTags").bind("click", function() {
					if (!showTagsFirst) {
						$("#loadingTags").show();
						$("#listExistingTags").load("{/literal}{$html->url('/tags/listAllTags/1')}{literal}", function() {
							$("#loadingTags").slideUp("fast");
							$("#listExistingTags").slideDown("fast");
							showTagsFirst = true;
							showTags = true;
						});
					} else {
						if (showTags) {
							$("#listExistingTags").slideUp("fast");
						} else {
							$("#listExistingTags").slideDown("fast");
						}
						showTags = !showTags;
					}
				});	
			});
			//-->
			</script>
			{/literal}
			
			<ul class="menuleft insidecol">
				<li>
					<a id="callTags" href="javascript:void(0)">{t}Select by tag{/t}</a>
				
				<div id="loadingTags" style="display:none" class="generalLoading" title="{t}Loading data{/t}">&nbsp;</div>
				<div id="listExistingTags" class="tag graced" style="display: none; padding:10px 15px 0px 0px;"></div>
				
				</li>
				
			</ul>


	{/if}

</div>

	