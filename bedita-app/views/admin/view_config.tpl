{literal}
<script type="text/javascript">
	$(document).ready(function(){
		var openAtStart ="#system_config";
		$(openAtStart).prev(".tab").BEtabstoggle();
	});
</script>
{/literal}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="viewConfig"}

{include file="inc/menucommands.tpl" method="viewConfig" fixed=true}

<div class="mainfull">


	<form action="{$html->url('/admin/saveConfig')}" method="post" name="configForm" id="configForm">


		<div class="tab"><h2>{t}System configuration{/t}</h2></div>

		<fieldset id="system_config">


			<table class="" border=0 style="margin-bottom:10px">

				<tr>
					<th>{t}Media root{/t}:</th>
					<td>
						<input type="text" name="sys[mediaRoot]" value="{$conf->mediaRoot}" style="width: 300px;"/>
					</td>
				</tr>

				<tr>
					<th>{t}Media url{/t}:</th>
					<td>
						<input type="text" name="sys[mediaUrl]" value="{$conf->mediaUrl}" style="width: 300px;"/>
					</td>
				</tr>

			</table>

		</fieldset>



		<div class="tab"><h2>{t}General configuration{/t}</h2></div>

		<fieldset id="system_config">


			<table class="" border=0 style="margin-bottom:10px">

				<tr>
					<th>{t}Project name{/t}:</th>
					<td>
						<input type="text" name="cfg[projectName]" value="{$conf->projectName}" style="width: 300px;"/>
					</td>
				</tr>

			</table>

		</fieldset>


	</form>

</div>