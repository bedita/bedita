<ul data-role="listview">
{foreach from=$objects item=object}
		<li>
				<a href="{$html->url('view/')}{$object.id}">
						<h3>{$object.title|truncate:64|default:"<i>[no title]</i>"}</h3>
						<p>{t}status{/t}: <strong>{$object.status}</strong>, {t}language{/t}: <strong>{$object.lang}</strong>, {t}comments{/t}: <strong>{$object.num_of_comment|default:0}</strong></p>
						<p>{t}last modified{/t}: <strong>{$object.modified|date_format:$conf->dateTimePattern}</strong></p>
				</a>
		</li>
{/foreach}
</ul>