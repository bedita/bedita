{literal}
<script type="text/javascript">
<!--
$(document).ready(function() {
	$('div.itemAssoc > ul').tabs();
});
//-->
</script>
{/literal}

<h2 class="showHideBlockButton">{t}Connect to other items{/t}</h2>
<div class="blockForm" id="frmAssocObject" style="display:none">
	<fieldset>
	<div class="itemAssoc">
		<ul>
			<li><a href="#docs"><span>{t}documents{/t}</span></a></li>
			<li><a href="#events"><span>{t}events{/t}</span></a></li>
			<li><a href="#news"><span>{t}news{/t}</span></a></li>
		</ul>
		
		<div id="docs">
		</div>
		
		<div id="events">
		</div>
		
		<div id="news">
		</div>
	</div>
	</fieldset>
</div>