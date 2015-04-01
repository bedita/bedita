<script type="text/javascript">
	var loadingtext = "{t}WAIT{/t}";
	var doneText = "{t}REDO{/t}";
	var retryText = "{t}RETRY{/t}";
	var confirmMsg = "{t}Are you sure you want to procede? The operation can take several time{/t}";
    $(document).ready(function() {
		openAtStart("#utilities");
			
		$("button").click(function() {
			if ($(this).hasClass("loading")) {
				return;
			}
			if (confirm(confirmMsg)) {
				$_this = $(this);
				var op = $_this.attr("rel");
				var chk = $("#cleanAll").is(':checked');
				var startText = $_this.text();
				$_this.text(loadingtext).removeClass("execute").addClass("loading");
				var postData = {
					operation: op,
					cleanAll: chk
				};
				postData = addCsrfToken(postData);
				$.ajax({
					url: "{$html->url('/admin/utility')}",
					data: postData,
					dataType: "json",
					type: "post",
					error: function(jqXHR, textStatus, errorThrown) {
						alert(errorThrown);
						$_this.text(retryText).removeClass("loading").addClass("execute");
					},
					success: function(data, textStatus, jqXHR) {
						$("#messagesDiv").empty();
						if (data.errorMsg != undefined) {
							//alert(data.errorMsg);
							if (data.htmlMsg != undefined && data.htmlMsg != "") {
								$("#messagesDiv").html(data.htmlMsg).triggerMessage("error");
							}
							$_this.text(retryText).removeClass("loading").addClass("execute");
						} else {
							//alert(data.message);
							if (data.htmlMsg != undefined && data.htmlMsg != "") {
								$("#messagesDiv").html(data.htmlMsg).triggerMessage(data.msgType);
							}
							$_this.text(doneText).removeClass("loading").addClass("execute");
						}
					}
				});
			}
		} ); 
    } );
</script>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl" fixed=true}

<div class="main">

		<div class="tab"><h2>{t}System utilities{/t}</h2></div>

			<table id="utilities" class="bordered">

				<tr>
					<th><b>{t}cleanup cache{/t}</b>:</th><td><button class="execute" rel="cleanupCache"> {t}GO{/t} </button></td>
					<td>{t}delete all cached files from BEdita and frontends{/t}
					<input type="checkbox" id="cleanAll" autocomplete="off"/><label>{t}Clean all cache files (also object cache){/t}</label>
					</td>
				</tr>
				<tr>
					<th><b>{t}empty logs{/t}</b>:</th><td><button class="execute" rel="emptyLogs"> {t}GO{/t} </button></td>
					<td>{t}empty log files{/t}</td>
				</tr>
				<tr>
					<th><b>{t}rebuild index{/t}</b>:</th><td><button class="execute" rel="rebuildIndex"> {t}GO{/t} </button></td>
					<td>{t}rebuild search texts index. Caution: may be slow{/t}</td>
				</tr>
				<tr>
					<th><b>{t}update stream fields{/t}</b>:</th><td><button class="execute" rel="updateStreamFields"> {t}GO{/t} </button></td>
					<td>{t}update name (if empty), mime_type (if empty), size and hash_file fields of streams table{/t}</td>
				</tr>
				<tr>
					<th><b>{t}clear media cache{/t}</b>:</th><td><button class="execute" rel="clearMediaCache"> {t}GO{/t} </button></td>
					<td>{t}clears media cache files/directories{/t}</td>	
				</tr>
					
			</table>


</div>