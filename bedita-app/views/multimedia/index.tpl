{$javascript->link("jquery/interface", false)}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.changealert", false)}

{*$javascript->link("jquery/jquery.MultiFile.pack", false)*}

<script type="text/javascript">
	
	
var urlGetObj		= '{$html->url("/streams/get_item_form_by_id")}' ;
var containerItem = "#multimediaItems";

{literal}
function commitUploadItem(IDs, rel) {

	//var currClass =  $(".multimediaitem:last").attr("class");
	//alert(currClass);
	var emptyDiv = "<div  class='multimediaitem itemBox gold'><\/div>";
	for(var i=0 ; i < IDs.length ; i++)
	{
		var id = escape(IDs[i]) ;

		$(emptyDiv).load(
			urlGetObj, {'id': id, 'relation':rel, 'template':'common_inc/file_item.tpl'}, function (responseText, textStatus, XMLHttpRequest)
			{
				$("#loading").hide();
				$(containerItem).append(this); 
			}
		)
	}	
}
{/literal}
</script>
</head>

<body>


{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="index"}

{include file="inc/menucommands.tpl" method="index" fixed=true}

{include file="../common_inc/toolbar.tpl"}

<div class="mainfull">

	{include file="../common_inc/list_streams.tpl" method="index" streamTitle="multimedia"}
	

	<div class="tab"><h2>{t}Add multiple items{/t}</h2></div>
	<div>
		<div id="loading" style="clear:both" class="multimediaitem itemBox small">&nbsp;</div>
		<div id="multimediaItems"></div>
			
		<div style="clear:both;">{include file="../common_inc/form_upload_multi.tpl"}</div>		
	</div>
	
</div>

