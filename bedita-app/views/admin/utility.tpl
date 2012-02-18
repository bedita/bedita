{literal}
<script type="text/javascript">
    $(document).ready(function(){
		openAtStart("#utilities");
    });
</script>
{/literal}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl" fixed=true}

<div class="main">

		<div class="tab"><h2>{t}System utilities{/t}</h2></div>

			<table id="utilities" class="bordered">

				<tr>
					<th><b>{t}cleanup files{/t}</b>:</th><td><button> GO </button></td>
					<td>{t}delete all backend tmp files{/t}</td>
				</tr>
				<tr>
					<th><b>{t}cleanup logs{/t}</b>:</th><td><button> GO </button></td>
					<td>{t}remove old items from log/job tables{/t}</td>
				</tr>
				<tr>
					<th><b>{t}rebuildIndex{/t}</b>:</th><td><button> GO </button></td>
					<td>{t}rebuild search texts index. Caution: may be slow{/t}</td>
				</tr>
				<tr>
					<th><b>{t}updateStreamFields{/t}</b>:</th><td><button> GO </button></td>
					<td>{t}update name (if empty), mime_type (if empty), size and hash_file fields of streams table{/t}</td>
				</tr>
				<tr>
					<th><b>{t}clearMediaCache{/t}</b>:</th><td><button> GO </button></td>
					<td>{t}clears media cache files/directories{/t}</td>	
				</tr>
					
			</table>


</div>