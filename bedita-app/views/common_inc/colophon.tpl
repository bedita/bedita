{literal}
<script type="text/javascript">
$(document).ready(function(){
	var cw = $("#cw").text();
	var ch = $("#ch").text();
	var rndm = Math.floor(Math.random()*2);
	if (rndm == 0) {
		$("#ch").text(cw);
		$("#cw").text(ch);
	}
});
</script>
{/literal}

<span class="belinks">
BEdita © <a href="http://www.bedita.com/chi-siamo-noi/" title="Chialab&Channelweb" target="besite">
	<strong id="ch">Chialab</strong> and <strong id="cw">Channelweb</strong> 2006-{$smarty.now|date_format:"%Y"}
<br />
<a href="http://www.bedita.com" title="BEdita web site" target="besite">› www.bedita.com</a>
</span>