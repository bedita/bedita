{$html->script("libs/dropzone/dropzone")}
<script>
	$(function(){
	// jQuery
	//$(".fileupload").dropzone({ url: "/quickitem/save" });
		$(".fileupload").dropzone();
	});
</script>
<form action="/" class="fileupload">
  <div class="fallback">
    <input name="file" type="file" multiple />
  </div>
</form>
