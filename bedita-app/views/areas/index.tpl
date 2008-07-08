{$javascript->link("jquery/jquery.treeview", true)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/ui/ui.core.min", false)}
{$javascript->link("jquery/ui/ui.sortable.min", false)}


<script type="text/javascript">
<!--
{literal}
$(document).ready(function(){
	/*
	designAreaTree() ;

	addCommand() ;
	refreshCommand() ;
	refreshOnClick() ;
	$("span.SectionItem").Draggable({
		revert:		true,
		ghosting:	true,
		opacity:	0.8
	});

	*/
	$("#areacontent").sortable ({
		distance: 20,
		opacity:0.7,
		update: $(this).reorderListItem
	}).css("cursor","move");
		
	$(".pubmodule").sortable ({
		distance: 20,
		opacity:0.7,
		//update: $(this).reorderListItem
	}).css("cursor","move");


});

{/literal}
//-->
</script>


</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" fixed=true}

<div class="head">
		
	<h2>{t}Publishing tree{/t}</h2>

</div> 

<div class="main" style="width:325px;">


		{include file="inc/form_tree.tpl"}


</div>


<div style="width:425px; position:absolute; top:180px; left:640px">

	<a href="{$html->url('viewSection/')}"><h2>"Programma"</h2></a>					
	<hr >
	<ul class="htab">
		<li rel="areacontentC">contenuti</li>
		<li rel="areapropertiesC">prorietà</li>
	</ul>				
	
	<div id="areacontentC" class="htabcontent" style="clear:none">
		<ul style="margin-top:10px; display: block;" id="areacontent" class="bordered">
			{section name=m loop=14}
			<li>
				<input type="text" class="priority" 
				style="text-align:right; margin-left: -30px; margin-right:10px; width:35px; float:left; background-color:transparent" 
				name="" value="{$smarty.section.m.iteration}" size="3" maxlength="3"/>
		
				<span class="listrecent documents" style="margin-left:0px">&nbsp;&nbsp;</span>
				<a title="2008-05-20 10:28:54" href="/documents/view/691">Nasce la Ctv</a>
				
			</li>
			{/section}
		</ul>		
		<hr>	
		<a href="#" class="graced" style="font-size:3em">‹ ›</a>
	</div>
	
	<div id="areapropertiesC" class="htabcontent" style="clear:none">					
			{*include file="inc/form_section.tpl"*}


			<table>
			
			<tr>
			
					<th>{t}Status{/t}:</th>
					<td>
						{html_radios name="data[status]" options=$conf->statusOptions 
						selected=$object.status|default:$conf->status separator="&nbsp;"}
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
					</select>
					</td>
				</tr>
				<tr>
					<th>{t}Title{/t}</th>
					<td><input type="text" name="data[title]" value="" /></td>
				</tr>
				<tr>
					<th>{t}Description{/t}</th>
					<td><textarea style="height:30px" class="autogrowarea" name="data[description]"></textarea></td>
			</tr>

			<tr>
				<td><label>reside in</label></td>
				<td><select id="areaSectionAssoc" class="areaSectionAssociation" name="data[destination]">
			{$beTree->option($tree)}
			</select></td>
			</tr>
			
			<tr>
				<td><label>{t}publisher{/t}</label></td>
				<td><input type="text" name="publisher" value="" /></td>
			</tr>
			<tr>
					<td><strong>&copy; {t}rights{/t}</strong></td>
				<td><input type="text" name="rights" value="" /></td>
			</tr>
			<tr>
				<td> <label>{t}license{/t}</label></td>                
				<td>
					<select style="width:200px;" name="license">
						<option value="">--</option>
						<option  value="1">Creative Commons Attribuzione 2.5 Italia</option>
						<option  value="2">Creative Commons Attribuzione-Non commerciale 2.5 Italia</option>
						<option  value="3">Creative Commons Attribuzione-Condividi allo stesso modo 2.5 Italia</option>
						<option  value="4">Creative Commons Attribuzione-Non opere derivate 2.5 Italia</option>
						<option  value="5">Creative Commons Attribuzione-Non commerciale-Condividi allo stesso modo 2.5 Italia</option>
						<option  value="6">Creative Commons Attribuzione-Non commerciale-Non opere derivate 2.5 Italia</option>
						<option  value="7">Tutti i diritti riservati</option>
					</select>
			    </td>
			</tr>
			
			
			</table>         
			
			<br>
			{assign var="section" value=quipewrproer}
			{include file="../common_inc/form_permissions.tpl" el=$section recursion=true}
			{include file="../common_inc/form_custom_properties.tpl" el=$section}
			
			
			<hr />
			oppure per i dettagli tipo custom pop e permessi linkare l'ulteriore dettaglio.?.
			<a href="{$html->url('viewSection/')}"> QUI</a>

	</div>
								
</div>















