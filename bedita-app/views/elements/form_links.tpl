{if isset($moduleList.webmarks)}
	<script type="text/javascript">
		var urlBaseAddLink = "{$html->url('/pages/addLink')}";

		function addItem() {
			var emptyLI = "<tr><\/tr>";
			var linkTitle = $("#linkTitle").val();
			var linkUrl = $("#linkUrl").val();
			var postData = {
				'title': linkTitle,
				'url': linkUrl
			};

			if (!linkTitle && !linkUrl) {
				return;
			}

			$("#loadingLinks").show();

			postData = addCsrfToken(postData);

			$(emptyLI).load(urlBaseAddLink, postData, function () {
				$("#listExistingLinks").append(this).fixItemsPriority() ;
				setupRemoveListeners();
				$("#loadingLinks").hide();
			});
		}

		function setupRemoveListeners() {
			$("#listExistingLinks").find(".remove").each(function() {
				$(this).click(function() {
					$(this).parents("tr").remove();
					$("#listExistingLinks").fixItemsPriority();
				});
			});
		}

		$(document).ready(function() {
			$("#addLink").click(function () {
				addItem();
				$(".new").val('');
			});
			setupRemoveListeners();
			$("#listExistingLinks").sortable({
				distance: 20,
				opacity: 0.7,
				update: $(this).fixItemsPriority
			});
		});
	</script>

	{$relcount = $relObjects.link|@count|default:0}
	<div class="tab">
		<h2 {if empty($relcount)}class="empty"{/if}>
			{t}Links{/t} &nbsp;
			{if $relcount > 0}<span class="relnumb">{$relcount}</span>{/if}
		</h2>
	</div>
	<fieldset id="links">
		<input type="hidden" name="data[RelatedObject][link][0][switch]" value="link" />
		<table class="indexlist">
			<thead>
				<tr>
					<th></th>
					<th>{t}title{/t}</th>
					<th>url</th>
					<th style="text-align:right">{t}commands{/t}</th>
				</tr>
			</thead>
			<tbody id="listExistingLinks" style="cursor: move">
				{if isset($relObjects.link)}
					{foreach from=$relObjects.link item="objRelated" name="linkForeach"}
						{assign_associative var="params" objRelated=$objRelated}
						<tr>{$view->element('form_link_item', $params)}</tr>
					{/foreach}
				{/if}
			</tbody>
			<tfoot style="border-top: 1px solid #d8d8d8">
				<tr id="loadingLinks" style="display:none">
					<td></td><td colspan="3">{t}Loading data{/t}</td>
				</tr>
				<tr id="newLink">
					<td></td>
					<td><input type="text" class="new" style="width: 100%" name="linkTitle" id="linkTitle" /></td>
					<td><input type="text" class="new" style="width: 100%" name="linkUrl" id="linkUrl" /></td>
					<td style="text-align: right"><input type="button" value="{t}add{/t}" id="addLink" /></td>
				</tr>
			</tfoot>
		</table>
	</fieldset>
{/if}

