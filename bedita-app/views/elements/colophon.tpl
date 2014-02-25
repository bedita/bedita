
<script type="text/javascript">
$(document).ready(function(){
	var cw = $("#cw").html();
	var ch = $("#ch").html();
	var rndm = Math.floor(Math.random()*2);
	if (rndm == 0) {
		$("#ch").html(cw);
		$("#cw").html(ch);
	}
});
</script>

<div class="belinks" style="padding:0px 10px">
<a href="http://www.bedita.com/who-are-we" title="Chialab&Channelweb" target="besite">BEdita {$conf->majorVersion} © </a>
	<strong id="ch"><a href="http://www.chialab.it" target="_blank">Chialab</a></strong> and <strong id="cw"><a href="http://www.channelweb.it" target="_blank"">ChannelWeb</a></strong> 
	2006-{$smarty.now|date_format:"%Y"}
<br />
<a href="http://www.bedita.com" title="BEdita web site" target="besite">www.bedita.com</a>
</div>
