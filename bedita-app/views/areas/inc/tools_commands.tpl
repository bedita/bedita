<div class="tab"><h2>{t}Tools{/t}</h2></div>

<div class="ignore">

{if $type=="section"}

		<a class="BEbutton" style="padding-left:30px; padding-right:20px;" href="{$html->url('/')}areas/viewSection/branch:{$object.id}">
			{t}create{/t}  {t}new section{/t} {t}here{/t} &nbsp;
		</a>

{else}

	<input style="width:140px" type="button" rel="{$html->url('/pages/showObjects/')}{$object.id|default:0}/0/0/leafs" class="modalbutton" value=" {t}add contents{/t} " />
	
	<div class="BEbutton" style="margin:10px 0px 10px 0px; width:295px; padding:5px;">
		{t}create new{/t} &nbsp;
		<select class="ignore selectcontenthere">
		{assign var=leafs value=$conf->objectTypes.leafs}
			{foreach from=$conf->objectTypes item=type key=key}	
				{if ( in_array($type.id,$leafs.id) && is_numeric($key) )}
				<option value="{$html->url('/')}{$type.module_name}/view/branch:{$object.id}" {if ($type.model=="Document")} selected="selected"{/if}>	
					{t}{$type.model}{/t}
				</option>
				{/if}
			{/foreach}
		</select>
		 &nbsp;
		{t}here{/t} &nbsp;
		<input type="button" class="newcontenthere" value="GO" />
	</div>
{/if}

	<input style="width:140px" type="button" rel="{$html->url('/pages/export/')}{$object.id|default:''}" class="modalbutton" value=" {t}export{/t} " />
	<input style="width:140px" type="button" rel="{$html->url('/pages/import/')}{$object.id|default:''}" class="modalbutton" value=" {t}import{/t} " />

</div>