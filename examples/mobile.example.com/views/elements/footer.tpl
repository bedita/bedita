<script type="text/javascript">
$(document).ready(function() { 
	// workaround: if android avoid transitions (issues in android 4.0)
	if (isAndroid40) {
		$("div#footer a").attr("data-transition", "none");
	}
});
</script>

<div id="footer" data-role="footer" data-position="fixed" data-id="persistent-bar">		
	<div data-role="navbar">
		<ul>
			<li><a href="#menu" data-transition="slideup" data-icon="grid"{if $active|default:'' == "menu"} data-theme="b"{/if}>{t}Menu{/t}</a></li>
			<li><a href="{$html->url('/tags')}" data-transition="slideup" data-icon="star"{if $view->action == "tags"} data-theme="b"{/if}>{t}Tags{/t}</a></li>
			<li><a href="#search" data-transition="slideup" data-icon="search" {if $active|default:'' == "search"} data-theme="b"{/if}>{t}Search{/t}</a></li>
			<li><a href="#credits" data-transition="slideup" data-icon="info"{if $active|default:'' == "credits"} data-theme="b"{/if}>{t}Credits{/t}</a></li>
		</ul>
	</div><!-- /navbar -->
</div><!-- /footer -->