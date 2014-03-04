{strip}
<div class="head">
	<div class="toolbar" style="white-space:nowrap">
		<h2>{t}Events calendar{/t}</h2>
		<table>
			<tr>
				<td>
					<a href="{$html->url('/')}events/calendar?{$nextCalendarDay|date_format:'Date_Day=%d&Date_Month=%m&Date_Year=%Y'}">
						{t}next 7 days{/t}
					</a>
				</td>
				
				<td>
					<a href="{$html->url('/')}events/calendar?{$prevCalendarDay|date_format:'Date_Day=%d&Date_Month=%m&Date_Year=%Y'}">
						{t}previous 7 days{/t}
					</a>
				</td>

				<td>
					<a href="{$html->url('/')}events/calendar?{$smarty.now|date_format:'Date_Day=%d&Date_Month=%m&Date_Year=%Y'}">
						{t}today{/t}
					</a>
				</td>
			</tr>
		</table>
	</div>
</div> 
{/strip}