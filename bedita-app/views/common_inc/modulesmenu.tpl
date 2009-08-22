{assign var='params' value=$html->params}
<script type="text/javascript">
var remote_url_response = "{$html->url('/pages/helpOnline/')}{$params.controller}/{$params.action}";
{literal}
$().ready(function(e){
	$('.helptrigger').click(function () {
		if( !($('#helpcontainer').is(':visible')) ) {	
				
			$("#helpcontainer").addClass("loadingHelp");
			
			$.get(remote_url_response, function(html){
				$(html).find(".textC").appendTo("#helpcontent");
				$("#helpcontainer").removeClass("loadingHelp");
			});			
		} else {
			$("#helpcontent").html("");
		}
		$('#helpcontainer, .quartacolonna, .main, .mainhalf, .mainfull, .insidecol').toggle();
		$(this).toggleClass("helpon");
	});
});
{/literal}
</script>
<div class="modulesmenucaption">go to: &nbsp;<a>be</a></div>

<ul class="modulesmenu">
		<li title="{t}help{/t}" class="helptrigger">?</li>
{strip}
{foreach from=$moduleListInv key=k item=mod}
{if ($mod.status == 'on')}
	{assign_concat var='link' 0=$html->url('/') 1=$mod.path}
	<li rel="{$link}" title="{t}{$mod.label}{/t}" class="{$mod.name} {if ($mod.name == $moduleName)} on{/if}"></li>
{/if}
{/foreach}

    <li rel="{$html->url('/')}" title="{t}Bedita3 main dashboard{/t}" class="bedita"></li>

{/strip}
</ul>