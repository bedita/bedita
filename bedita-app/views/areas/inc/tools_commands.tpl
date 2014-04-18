<div class="tab"><h2>{t}Tools{/t}</h2></div>

<div class="ignore">

{if $type=="section"}

		<a class="BEbutton" style="padding-left:30px; padding-right:20px;" href="{$html->url('/')}areas/viewSection/branch:{$object.id}">
			{t}create{/t}  {t}new section{/t} {t}here{/t} &nbsp;
		</a>

{else}

	<input style="width:140px" type="button" rel="{$html->url('/pages/showObjects/')}{$object.id|default:0}/0/0/leafs" class="modalbutton" value=" {t}add contents{/t} " />
{/if}

	<input style="width:140px" type="button" rel="{$html->url('/pages/export/')}{$object.id|default:''}" class="modalbutton" value=" {t}export{/t} " />
	<input style="width:140px" type="button" rel="{$html->url('/pages/import/')}{$object.id|default:''}" class="modalbutton" value=" {t}import{/t} " />

</div>