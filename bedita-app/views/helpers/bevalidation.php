<?
/**
 * BevalidatorHelper helper library.
 *
 * Aiuta ad inserire il validatore dei campi di  o + form.
 *
 * @package		
 * @subpackage	
 */
class BevalidationHelper extends Helper {
	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	var $helpers = array('Html', 'Javascript');
	
	
	/*
	* Inserisce i tag script con i file necessari
	* @params string $lang	codice lingua selezioanta x YAV: it (default), en,de, es, fr, pt-BR, sk
	*/
	function setup($lang = "it") {
		$tmp  = $this->Javascript->link("yav") . "\n" ;
		$tmp .= $this->Javascript->link("yav-config-$lang") . "\n" ;
		$tmp .= $this->Javascript->link("BEValidation") ."\n" ;
		
		return $tmp ;
	}
	
	/*
	 * Modifica di formTag HtmlHelper per inserire il controllo con onsubmit
	 *
	 * @param string $ID noem e ID che assume il form
	 * @param string $alertType Come visualizza il msg d'errore: classic (alert), innerHtml, jsVar
	 * @param string $target URL for the FORM's ACTION attribute.
	 * @param string $type		FORM type (POST/GET).
	 * @param array  $htmlAttributes
	 * @return string An formatted opening FORM tag.
	*/
	function formTag($ID, $alertType = "", $target = null, $type = 'post', $htmlAttributes = array()) {
		$htmlAttributes["id"] 		= $ID ;
		$htmlAttributes["name"] 	= $ID ;
		$htmlAttributes["onsubmit"] = "return BEValidation.validate('$ID', '$alertType')" ;
		
		return $this->Html->formTag($target, $type, $htmlAttributes) ;
	}
	
	/*
	* Aggiunge una nuova regola
	* @param string $IDForm Id del form a cui appartiene il campo
	* @param string $rule regola da applicare
	*/
	function rule($IDForm, $rule) {
		$script = "BEValidation.addField('$IDForm', '$rule') ;" ;
		
		return $this->Javascript->codeBlock($script) ;
	}
}

?>