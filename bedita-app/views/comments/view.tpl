
{$javascript->link("jquery/jquery.form")}

{literal}
<script type="text/javascript">
    $(document).ready(function(){
		
		var openAtStart ="#details,#altro";
		$(openAtStart).prev(".tab").BEtabstoggle();
		
    });
</script>
{/literal}
<script type="text/javascript">
var urlBan = "{$html->url('/comments/banIp')}";
var msgBan = "{t}Are you sure you want to ban this IP?{/t}";
var msgAccept = "{t}Are you sure you want to accept this IP?{/t}";
{literal}
$(document).ready(function(){
	$("#banIP").bind("click", function(){
		if(!confirm(msgBan)) return false ;
		$("#updateForm").attr("action", urlBan).submit();
		return false;
	});
	$("#sbanIP").bind("click", function(){
		if(!confirm(msgAccept)) return false ;
		$("#updateForm").attr("action", urlBan).submit();
		return false;
	});
});
{/literal}
</script>

</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl"}

<div class="head">
	
	<h1>{if !empty($object)}{$object.title|default:"<i>[no title]</i>"}{else}<i>[{t}New item{/t}]</i>{/if}</h1>

</div>

{assign var=objIndex value=0}


{include file="inc/menucommands.tpl" fixed=true method="view"}


<form action="{$html->url('/comments/save')}" method="post" name="updateForm" id="updateForm" class="cmxform">
<input type="hidden" name="data[id]" value="{$object.id|default:''}"/>
<input type="hidden" name="data[title]" value="{$object.title|default:''}" />
<input type="hidden" name="data[nickname]" value="{$object.nickname|default:''}" />

<div class="main">	
	
	{include file="inc/form.tpl"}
		
</div>


{include file="../common_inc/menuright.tpl"}


</form>


