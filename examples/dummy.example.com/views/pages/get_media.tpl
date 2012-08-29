{agent var=agent}
{if (($object.application_name|default:'') == "flash")}

	{literal}
		<script type="text/javascript">
		
			var flashvars = {
			  uri: "{/literal}{$conf->mediaUrl}/{$object.path|default:$object.uri}{literal}",
			  baseurl: "{/literal}{$html->url('/')}{literal}"
			};
			
			var params = {
			   play: "true"
			  
			};

	    	swfobject.embedSWF("{/literal}{$conf->mediaUrl}/{$object.path|default:$object.uri}{literal}", "myContentBig", "{/literal}{$object.width|default:800}{literal}", "{/literal}{$object.height|default:600}{literal}", "9.0.0","expressInstall.swf", flashvars, params);
	    
	    </script>
	    
	{/literal}
	
	<div id="myContentBig" style="background-color:white;">
     	<p>Alternative content</p>
	</div>

	<div class="dida" style="color:#666; margin:10px">
		{$object.title}. {$object.description|nl2br}
	</div>
	
{elseif (($object.mime_type|default:'') == "video/blip" or $object.mime_type|default:'' == "video/vimeo" or $object.mime_type|default:'' == "video/youtube")}
	
	<!-- se è iphone o ipad ricostruisce l'utl per il nuovo embed con fallback -->
	{if $agent.iPHONE or $agent.iPAD}
		<iframe width="560" height="468" src="{$object.uri|replace:'watch?v=':'embed/'}" frameborder="0" allowfullscreen></iframe>
	{else}
		{assign_associative var="htmlAttr" width="560" height="468"}
		{$beEmbedMedia->object($object, $conf->flowPlayerParams, $htmlAttr)}
	{/if}

{elseif (($object.object_type|default:'') == "Video")}

	<a href="{$conf->mediaUrl}/{$object.path|default:$object.uri}"  style="position:relative; display:block; width:560px; height:420px;"  id="player">
			
	</a> 
	
	<div id="appleControls" class="controls"></div>
		{literal}
				<script type="text/javascript">
					
					flowplayer("player", {src: '../flowplayer-3.2.7.swf', wmode: 'opaque'},  { 
					    clip: {autoPlay: true, autoBuffering: true},
						plugins:  {	
						controls:  {
							backgroundColor: '#FFFFFF',
							backgroundGradient: 'none',	
							timeColor: '#0099cc',
							buttonColor: '#999999',
							buttonOverColor: '#0099cc',
							all:true,
							scrubber:true,
							mute:true,
							height:30,
							autoHide: 'always',
							progressColor: '#333333',
							bufferColor: '#DEDEDE'
							
						}	
					}	
				}).ipad({ simulateiDevice: false, controls: true });
				</script>
				
		{/literal}
<!-- se è un video kaltura && ($object.mime_type == "application/x-shockwave-flash") -->
{elseif !empty($object.Category[0].name) && ($object.Category[0].name == 'video') && empty($object.mime_type)}
	
		{$object.note}	
		
			
		
{else}

	{assign_associative var="htmlAttributes" alt=$object.title} 
		{assign_associative var="paramsBig" width="655" mode="fill" upscale=false}
		
			{$beEmbedMedia->object($object,$paramsBig,$htmlAttributes)}
	
		
		{*<div class="dida">
			
			
			{assign_concat var="file" 0=$conf->mediaUrl 1=$object.path|default:$object.uri}
			<div class="modulo10" style="float:right; margin-right:0px!important;">
				<a class="modalclose ico medium" id="close">chiudi</a>
			</div>
		</div>*}

{/if}
		

<div class="dida">
	{$object.description|nl2br}
</div>

<br style="clear:both;" />

<hr />

<a class="modalclose" id="close"><img class="ico medium" style="vertical-align:middle;" src="/img/ico_close.png" /> chiudi</a>


