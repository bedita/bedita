{*
** multimedia view template
** @author ChannelWeb srl
*}
{$javascript->link("jquery/jquery.form")}
{$javascript->link("jquery/ui/ui.datepicker.min", false)}

<script type="text/javascript">
{if !empty($object.path)}
{literal}

    $(document).ready(function(){
		openAtStart("#multimediaitem");
    });
{/literal}
{else}
{literal}

    $(document).ready(function(){
		openAtStart("#title,#mediatypes");
    });
{/literal}
{/if}
</script>

{include file="../common_inc/form_common_js.tpl"}

</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="view"}

<div class="head">
	
	<h1>{if !empty($object)}{$object.title|default:"<i>[no title]</i>"}{else}<i>[{t}New item{/t}]</i>{/if}</h1>

</div>

{include file="inc/menucommands.tpl" method="view" fixed=true}

<form action="{$html->url('/multimedia/save')}" enctype="multipart/form-data" method="post" name="updateForm" id="updateForm" class="cmxform">
<input  type="hidden" name="data[id]" value="{$object.id|default:''}" />
<input  type="hidden" name="data[object_type_id]" value="{$object.object_type_id|default:''}" />
<input  type="hidden" name="data[path]" value="{$object.path}" />
<input  type="hidden" name="data[name]" value="{$object.name}" />
<input  type="hidden" name="data[mime_type]" value="{$object.mime_type}" />

<div class="main">

	{include file="inc/form.tpl"}	

</div>

{include file="../common_inc/menuright.tpl"}

</form>
