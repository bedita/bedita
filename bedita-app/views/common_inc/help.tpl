{*
** Help container
** Included in layout/default.tpl
*}



<div id="helpcontainer">
	<div class="graced" style="margin:20px; color:#333; font-size:14px;">
		<h2 class="bedita">
			BEhelp } {$currentModule.label} } {$html->action}
		</h2>
		<hr />
		<div id="helpcontent"></div>
		{*
		<p>Usually, a questionnaire consists of a number of questions that the respondent has to answer in a set format. A distinction is made between open-ended and closed-ended questions. An open-ended question asks the respondent to formulate his own answer, whereas a closed-ended question has the respondent pick an answer from a given number of options. The response options for a closed-ended question should be exhaustive and mutually exclusive. Four types of response scales for closed-ended questions are distinguished:</p>
		<ul>
			<li>Dichotomous, where the respondent has two options</li>
			<li>Nominal-polytomous, where the respondent has more than two unordered options</li>
			<li>Ordinal-polytomous, where the respondent has more than two ordered options</li>
			<li>(bounded)Continuous, where the respondent is presented with a continuous scale</li>
		</ul>
		<p>A respondents answer to an open-ended question is coded into a response scale afterwards.</p>
		*}
	</div>

</div>

