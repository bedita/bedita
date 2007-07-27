/*
 * Form Validation: jQuery form validation plug-in v1.1
 *
 * http://bassistance.de/jquery-plugins/jquery-plugin-validation/
 *
 * Copyright (c) 2006 Jörn Zaefferer
 *
 * $Id: jquery.validate.js 2133 2007-06-21 18:50:13Z joern.zaefferer $
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 */

/**
 * Validates a single form.
 *
 * The normal behaviour is to validate a form when a submit button is clicked or
 * the user presses enter when an input of that form is focused.
 *
 * It is also possible to validate each individual element of that form, eg. on blur or keyup.
 *
 * @example $("#myform").validate();
 * @before <form id="myform">
 *   <input name="firstname" class="{required:true}" />
 * </form>
 * @desc Validates a form on submit. Rules are read from metadata.
 *
 * @example $("input").validate({
 * 		event: "blur",
 *		success: "valid"
 * });
 * @desc Validates all input elements on blur event (when the element looses focus).
 * Adds a class "valid" to all error labels when the elements are valid and keeps showing
 * them.
 *
 * @example $("#myform").validate({
 *   submitHandler: function(form) {
 *   	$(form).ajaxSubmit();
 *   }
 * });
 * @desc Uses form plugin's ajaxSubmit method to handle the form submit, while preventing
 * the default submit.
 *
 * @example $("#myform").validate({
 *  event: "keyup"
 * 	rules: {
 * 		"first-name": "required",
 * 		age: {
 *			required: "#firstname:blank",
 * 			number: true,
 * 			minValue: 3
 * 		},
 * 		password: {
 * 			required: function() {
 * 				return $("#age").val() < 18;
 * 			},
 * 			minLength: 5,
 * 			maxLength: 32
 * 		}
 * 	},
 *  messages: {
 * 		password: {
 * 			required: function(element, validator) {
 * 				return "Your password is required because with " + $("#age").val() + ", you are not old enough yet."
 * 			},
 * 			minLength: "Please enter a password at least 5 characters long.",
 * 			maxLength: "Please enter a password no longer then 32 characters long."
 * 		},
 *		age: "Please specify your age as a number (at least 3)."
 * 	}
 * });
 * @desc Validate a form on submit and each element on keyup. Rules are specified
 * for three elements, and a message is customized for the "password" and the
 * "age" elements. Inline rules are ignored. The password is only required when the age is lower
 * then 18. The age is only required when the firstname is blank. Note that "first-name" is quoted, because
 * it isn't a valid javascript identifier. "first-name": "required" also uses a shortcut replacement for
 * { required: true }. That works for all trivial validations that expect no more then a boolean-true argument.
 * The required-message for password is specified as a function to use runtime customization.
 *
 * @example $("#myform").validate({
 *   errorClass: "invalid",
 *   errorLabelContainer: $("#messageBox"),
 *   wrapper: "li"
 * });
 * @before <ul id="messageBox"></ul>
 * <form id="myform" action="/login" method="post">
 *   <label>Firstname</label>
 *   <input name="fname" class="{required:true}" />
 *   <label>Lastname</label>
 *   <input name="lname" title="Your lastname, please!" class="{required:true}" />
 * </form>
 * @result <ul id="messageBox">
 *   <li><label for="fname" class="invalid">Please specify your firstname!</label></li>
 *   <li><label for="lname" class="invalid">Your lastname, please!</label></li>
 * </ul>
 * <form id="myform" action="/login" method="post">
 *   <label>Firstname</label>
 *   <input name="fname" class="{required:true} invalid" />
 *   <label>Lastname</label>
 *   <input name="lname" title="Your lastname, please!" class="{required:true} invalid" />
 * </form>
 * @desc Validates a form on submit. The class used to search, create and display
 * error labels is changed to "invalid". This is also added to invalid elements.
 *
 * All error labels are displayed inside an unordered list with the ID "messageBox", as
 * specified by the jQuery object passed as errorContainer option. All error elements
 * are wrapped inside an li element, to create a list of messages.
 *
 * To ease the setup of the form, debug option is set to true, preventing a submit
 * of the form no matter of being valid or not.
 *
 *
 * @example $("#myform").validate({
 * 	errorPlacement: function(error, element) {
 * 		error.appendTo( element.parent("td").next("td") );
 * 	},
 * 	success: function(label) {
 * 		label.text("ok!").addClass("success");
 * 	}
 * });
 * @before <form id="myform" action="/login" method="post">
 * 	<table>
 * 		<tr>
 * 			<td><label>Firstname</label>
 * 			<td><input name="fname" class="{required:true}" value="Pete" /></td>
 * 			<td></td>
 * 		</tr>
 * 		<tr>
 * 			<td><label>Lastname</label></td>
 * 			<td><input name="lname" title="Your lastname, please!" class="{required:true}" /></td>
 * 			<td></td>
 * 		</tr>
 * 	</table>
 * </form>
 * @result <form id="myform" action="/login" method="post">
 * 	<table>
 * 		<tr>
 * 			<td><label>Firstname</label>
 * 			<td><input name="fname" class="{required:true}" value="Pete" /></td>
 * 			<td><label for="fname" class="invalid success">ok!</label></td>
 * 		</tr>
 * 		<tr>
 * 			<td><label>Lastname</label></td>
 * 			<td><input name="lname" title="Your lastname, please!" class="{required:true}" /></td>
 * 			<td><label for="lname" class="invalid">Your lastname, please!</label></td>
 * 		</tr>
 * 	</table>
 * </form>
 * @desc Validates a form on submit. Customizes the placement of the generated labels
 * by appending them to the next table cell. Displays "ok!" for valid elements and adds a class
 * "success" to the message (the class "invalid" is kept to identify the error label).
 *
 * @example $("#myform").validate({
 *   errorContainer: $("#messageBox1, #messageBox2"),
 *   errorLabelContainer: $("#messageBox1 ul"),
 *   wrapper: "li",
 * });
 * @before <div id="messageBox1">
 *   <h3>The are errors in your form!</h3>
 *   <ul></ul>
 * </div>
 * <form id="myform" action="/login" method="post">
 *   <label>Firstname</label>
 *   <input name="fname" class="{required:true}" />
 *   <label>Lastname</label>
 *   <input name="lname" title="Your lastname, please!" class="{required:true}" />
 * </form>
 * <div id="messageBox2">
 *   <h3>The are errors in your form, see details above!</h3>
 * </div>
 * @result <ul id="messageBox">
 *   <li><label for="fname" class="error">Please specify your firstname!</label></li>
 *   <li><label for="lname" class="error">Your lastname, please!</label></li>
 * </ul>
 * <form id="myform" action="/login" method="post">
 *   <label>Firstname</label>
 *   <input name="fname" class="{required:true} error" />
 *   <label>Lastname</label>
 *   <input name="lname" title="Your lastname, please!" class="{required:true} error" />
 * </form>
 * @desc Validates a form on submit. Similar to the above example, but with an additional
 * container for error messages. The elements given as the errorContainer are all shown
 * and hidden when errors occur. But the error labels themselve are added to the element(s)
 * given as errorLabelContainer, here an unordered list. Therefore the error labels are
 * also wrapped into li elements (wrapper option).
 *
 * @param Map options Optional settings to configure validation
 * @option String errorClass Use this class to look for existing error labels and add it to
 *		invalid elements. Default: "error"
 * @option String wrapper Wrap error labels with the specified element, eg "li". Default: none
 * @option Boolean debug If true, the form is not submitted and certain errors are display on the console (requires Firebug or Firebug lite). Default: none
 * @option Boolean focusInvalid Focus the last active or first invalid element on submit or via validator.focusInvalid(). Default: true
 * @option Function submitHandler Callback for handling the actual
 *		submit when the form is valid. Gets the form as the only argmument. Default: normal form submit
 * @option Map<String, Object> messages Key/value pairs defining custom messages.
 *		Key is the name of an element, value the message to display for that element. Instead of
 *		a plain message	another map with specific messages for each rule can be used.
 *		Can be specified for one or more elements. Overrides the title attribute of an element.
 *		Each message can be a String or a Function. The Function is called with the element
 * 		as the first and the validator as the second argument and must return a String to display as the message
 * 		for that element.
 *      Default: none, the default message for the method is used.
 * @option Map<String, Object> rules Key/value pairs defining custom rules.
 *		Key is the ID or name (for radio/checkbox inputs) of an element,
 *		value is an object consisting of rule/parameter pairs, eg. {required: true, min: 3}.
 *   	Once specified, metadata rules are completely ignored.
 *		Default: none, rules are read from metadata via metadata plugin
 * @option Boolean onsubmit Validate the form on submit. Set to false to use only other
 *		events for validation (option event). Default: true
 * @option String meta In case you use metadata for other plugins, too, you
 *		want to wrap your validation rules
 *		into their own object that can be specified via this option. Default: none
 * @option jQuery errorContainer Hide and show this container when validating. Default: none
 * @option jQuery errorLabelContainer Search and append error labels to this container, and show and hide it accordingly. Default: none
 * @option Function showErrors A custom message display handler. Gets the map of errors as the
 *		first argument and a refernce to the validator object as the second.
 * 		You can trigger (in addition to your own messages) the default behaviour by calling
 * 		the defaultShowErrors() method of the validator.
 * 		Default: none, uses built-in message disply.
 * @option Function errorPlacement Used to customize placement of created error labels.
 *		First argument: jQuery object containing the created error label
 *		Second argument: jQuery object containing the invalid element
 *		Default: Places the error label after the invalid element
 * @option String errorElement The element to use for generated error messages. Default: "label"
 * @option String|Function If specified, the error label is displayed to show a valid element. If 
 * 		a String is given, its added as a class to the label. If a Function is given, its called with
 *		the label (as a jQuery object) as its only argument. That can be used to add a text like "ok!".
 *		Default: none
 * @option Boolean focusCleanup If enabled, removes the errorClass from the invalid elements and hides
 * 		all errors messages whenever the element is focused. Avoid combination with focusInvalid. Default: false
 * @option String|Element ignore Elements to ignore when validating, simply filtering them out. jQuery's not-method
 * 		is used, there everything that is accepted by not() can be passed as this option. Default: None, though inputs of type
 *		submit and reset are always ignored.
 * @opton Boolean onblur Validate elements on blur. If nothing is entered, all rules are skipped, 
 * 		except when the field was already marked as invalid. Default: true
 *
 * @name validate
 * @type $.validator
 * @cat Plugins/Validate
 */

jQuery.extend(jQuery.fn, {
	validate: function( options ) {
		var validator = new jQuery.validator( options, this[0] );
		
		if ( validator.settings.onsubmit ) {
		
			// allow suppresing validation by adding a cancel class to the submit button
			this.find("input.cancel:submit").click(function() {
				this.form.cancel = true;
			});
		
			// validate the form on submit
			this.submit( function( event ) {
				if ( validator.settings.debug )
					// prevent form submit to be able to see console output
					event.preventDefault();
					
				// prevent submit for invalid forms or custom submit handlers
				if ( this.cancel || validator.form() ) {
					this.cancel = false;
					if ( validator.settings.submitHandler ) {
						validator.settings.submitHandler( validator.currentForm );
						return false;
					}
					return true;
				} else {
					validator.focusInvalid();
					return false;
				}
			});
		}
		
		validator.settings.onblur && validator.elements.blur( function() {
			validator.settings.onblur.call( validator, this );
		});
		validator.settings.onkeyup && validator.elements.keyup(function() {
			validator.settings.onkeyup.call( validator, this );
		});
		var checkables = jQuery([]);
		validator.elements.each(function() {
			if ( validator.checkable( this ) )
				checkables.push( validator.checkableGroup( this ) );
		});
		validator.settings.onchange && checkables.click(function() {
			validator.settings.onchange.call( validator, this );
		});
		
		return validator;
	},
	// destructive add
	push: function( t ) {
		return this.setArray( jQuery.merge( this.get(), t ) );
	}
});

/**
 * Expression to filter for blank fields.
 *
 * @example jQuery("input:blank").length
 * @before <input value="" /><input value="  " /><input value="abc" />
 * @result 2
 *
 * @property
 * @type String
 * @name :blank
 * @cat Plugins/Validate
 */
 
/**
 * Expression to filter for filled fields.
 *
 * @example jQuery("input:filled").length
 * @before <input value="" /><input value="  " /><input value="abc" />
 * @result 1
 *
 * @property
 * @type String
 * @name :filled
 * @cat Plugins/Validate
 */
 
/**
 * Expression to filter unchecked checkboxes or radio buttons.
 *
 * @example jQuery("input:unchecked").length
 * @before <input type="checkbox" /><input type="checkbox" checked="checked" />
 * @result 1
 *
 * @property
 * @type String
 * @name :unchecked
 * @cat Plugins/Validate
 */
jQuery.extend(jQuery.expr[":"], {
	blank: "!jQuery.trim(a.value)",
	filled: "!!jQuery.trim(a.value)",
	unchecked: "!a.checked"
});

/**
 * Simple string-templating, similar to Java's MessageFormat.
 *
 * Accepts a string template as the first argument. The second is optional:
 * If specified, it is used to replace placeholders in the first argument.
 *
 * It can be an Array of values or any other type, in which case only one value
 * is replaced.
 *
 * If the second argument is ommited, a function is returned that expects the value-argument
 * to return the formatted value (see example).
 *
 * @example String.format("Please enter a value no longer then {0} characters.", 0)
 * @result "Please enter a value no longer then 0 characters."
 * @desc Formats a string with a single argument.
 *
 * @example String.format("Please enter a value between {0} and {1}.", 0, 1)
 * @result "Please enter a value between 0 and 1."
 * @desc Formats a string with two arguments. Same as String.format("...", [0, 1]);
 *
 * @example String.format("Please enter a value no longer then {0} characters.")(0);
 * @result "Please enter a value no longer then 0 characters."
 * @desc String.format is called at first without the second argument, returning a function that is called immediately
 * 		 with the value argument. Useful to defer the actual formatting to a later point without explicitly 
 *		 writing the function.
 *
 * @type String
 * @name String.format
 * @cat Plugins/Validate
 */
String.format = function(source, params) {
	if ( arguments.length == 1 ) 
		return function( param ) {
			return String.format( source, param );
		};
	if ( arguments.length > 2 )
		params = jQuery.makeArray(arguments).slice(1);
	if ( params.constructor != Array )
		params = [ params ];
	jQuery.each(params, function(i, n) {
		source = source.replace(new RegExp("\\{" + i + "\\}"), n);
	});
	return source;
};

// constructor for validator
jQuery.validator = function( options, form ) {
	this.settings = jQuery.extend( {}, jQuery.validator.defaults, options );

	this.currentForm = form;
	this.labelContainer = this.settings.errorLabelContainer;
	this.errorContext = this.labelContainer.length && this.labelContainer || jQuery(form);
	this.containers = this.settings.errorContainer.add( this.settings.errorLabelContainer );
	this.submitted = {};
	this.reset();
	this.refresh();
};

jQuery.extend(jQuery.validator, {

	defaults: {
		messages: {},
		errorClass: "error",
		errorElement: "label",
		focusInvalid: true,
		errorContainer: jQuery( [] ),
		errorLabelContainer: jQuery( [] ),
		onsubmit: true,
		ignore: [],
		onblur: function(element) {
			if ( !this.checkable(element) && (element.name in this.submitted || !this.required(element)) ) {
				this.element(element);
			}
		},
		onkeyup: function(element) {
			if ( element.name in this.submitted || element == this.lastElement ) {
				this.element(element);
			}
		},
		onchange: function(element) {
			if ( element.name in this.submitted )
				this.element(element);
		}
	},

	/**
	 * Modify default settings for validation.
	 *
	 * @example jQuery.validator.setDefaults({
	 * 	debug: true
	 * );
	 * @desc Sets the debug setting for all validation calls following.
	 *
	 * @param Object<String, Object> settings
	 * @name jQuery.validator.setDefaults
	 * @type undefined
	 * @cat Plugins/Validate
	 */
	setDefaults: function(settings) {
		jQuery.extend( jQuery.validator.defaults, settings );
	},

	/**
	 * Default messages for all default methods.
	 *
	 * Use addMethod() to add methods with messages.
	 *
	 * Replace these messages for localization.
	 *
	 * @property
	 * @type String
	 * @name jQuery.validator.messages
	 * @cat Plugins/Validate
	 */
	messages: {
		required: "This field is required.",
		email: "Please enter a valid email address.",
		url: "Please enter a valid URL.",
		date: "Please enter a valid date.",
		dateISO: "Please enter a valid date (ISO).",
		dateDE: "Bitte geben Sie ein gültiges Datum ein.",
		number: "Please enter a valid number.",
		numberDE: "Bitte geben Sie eine Nummer ein.",
		digits: "Please enter only digits",
		creditcard: "Please enter a valid credit card.",
		equalTo: "Please enter the same value again.",
		accept: "Please enter a value with a valid extension.",
		maxLength: String.format("Please enter a value no longer than {0} characters."),
		minLength: String.format("Please enter a value of at least {0} characters."),
		rangeLength: String.format("Please enter a value between {0} and {1} characters long."),
		rangeValue: String.format("Please enter a value between {0} and {1}."),
		maxValue: String.format("Please enter a value less than or equal to {0}."),
		minValue: String.format("Please enter a value greater than or equal to {0}.")
	},
	
	prototype: {

		/**
		 * Validate on instant the entire form.
		 *
		 * @example $("#myform").validate().form();
		 * @desc Triggers form validation programmatcitally.
		 *
		 * @name jQuery.validator.protoype.form
		 * @type Boolean True when the form is valid, otherwise false
		 * @cat Plugins/Validate
		 */
		form: function() {
			this.prepareForm();
			for ( var i = 0, element; element = this.elements[i]; i++ ) {
				this.check( element );
			}
			jQuery.extend(this.submitted, this.errorMap);
			return this.valid();
		},

		/**
		 * Validate on instant a single element.
		 *
		 * @example $("#myform").validate().element( "#myselect" );
		 * @desc Triggers validation on a single element programmatically.
		 *
		 * @param String|Element element A selector or an element to validate
		 *
		 * @name jQuery.validator.protoype.element
		 * @type Boolean True when the element is valid, otherwise false
		 * @cat Plugins/Validate
		 */
		element: function( element ) {
			element = this.clean( element );
			this.lastElement = element;
			this.prepareElement( element );
			var result = this.check( element );
			this.showErrors();
			return result;
		},

		/**
		 * Show the specified messages.
		 *
		 * @example var validator = $("#myform").validate();
		 * validator.showErrors({"firstname": "I know that your firstname is Pete, Pete!"});
		 * @desc Adds and shows error message programmatically.
		 *
		 * @param Map errors One or more key/value pairs of input names and messages
		 *
		 * @name jQuery.validator.protoype.showErrors
		 * @cat Plugins/Validate
		 */
		showErrors: function(errors) {
			if(errors) {
				// add items to error list and map
				jQuery.extend( this.errorMap, errors );
				for ( name in errors ) {
					this.errorList.push({
						message: errors[name],
						element: jQuery("[@name=" + name + "]:first", this.currentForm)[0]
					});
				}
				// remove items from success list
				this.successList = jQuery.grep( this.successList, function(element) {
					return !(element.name in errors);
				});
			}
			this.settings.showErrors
				? this.settings.showErrors.call( this, this.errorMap, this.errorList )
				: this.defaultShowErrors();
		},
		
		/**
		 * Resets the controlled form, including resetting input fields
		 * to their original value (requires form plugin), removing classes
		 * indicating invalid elements and hiding error messages.
		 *
		 * @example var validator = $("#myform").validate();
		 * validator.resetForm();
		 * @desc Reset the form controlled by this validator.
		 *
		 * @name jQuery.validator.protoype.resetForm
		 * @cat Plugins/Validate
		 */
		resetForm: function() {
			if( jQuery.fn.resetForm )
				jQuery( this.currentForm ).resetForm();
			this.prepareForm();
			this.hideErrors();
			this.elements.removeClass( this.settings.errorClass );
		},
		
		hideErrors: function() {
			this.addWrapper( this.toHide ).hide();
		},
		
		valid: function() {
			this.showErrors();
			return this.errorList.length == 0;
		},
		
		focusInvalid: function() {
			if( this.settings.focusInvalid ) {
				try {
					jQuery(this.findLastActive() || this.errorList.length && this.errorList[0].element || []).filter(":visible").focus();
				} catch(e) { /* ignore IE throwing errors when focusing hidden elements */ }
			}
		},
		
		findLastActive: function() {
			var lastActive = this.lastActive;
			return lastActive && jQuery.grep(this.errorList, function(n) {
				return n.element.name == lastActive.name;
			}).length == 1 && lastActive;
		},
		
		refresh: function() {
			var validator = this;
			validator.rulesCache = {};
			
			// select all valid inputs inside the form (no submit or reset buttons)
			this.elements = jQuery(this.currentForm)
			.find("input, select, textarea, button")
			.not(":submit")
			.not(":reset")
			.not( this.settings.ignore )
			.filter(function() {
				!this.name && validator.settings.debug && window.console && console.error( "%o has no name assigned", this);
			
				// select only the first element for each name, and only those with rules specified
				if ( this.name in validator.rulesCache || !validator.rules(this).length )
					return false;
				
				validator.rulesCache[this.name] = validator.rules(this);
				return true;
			});
			
			
			// and listen for focus events to save reference to last focused element
			this.elements.focus(function() {
				validator.lastActive = this;
				
				// hide error label and remove error class on focus if enabled
				if ( validator.settings.focusCleanup ) {
					jQuery(this).removeClass( validator.settings.errorClass );
					validator.errorsFor(this).hide();
				}
			});
		},
		
		clean: function( selector ) {
			return jQuery( selector )[0];
		},
		
		errors: function() {
			return jQuery( this.settings.errorElement + "." + this.settings.errorClass, this.errorContext );
		},
		
		reset: function( element ) {
			this.successList = [];
			this.errorList = [];
			this.errorMap = {};
			this.toShow = jQuery( [] );
			this.toHide = jQuery( [] );
		},
		
		prepareForm: function() {
			this.reset();
			//this.submitted = {};
			this.toHide = this.errors().push( this.containers );
		},
		
		prepareElement: function( element ) {
			this.reset();
			this.toHide = this.errorsFor( this.clean(element) );
		},
	
		check: function( element ) {
			element = this.clean( element );
			jQuery( element ).removeClass( this.settings.errorClass );
			//var rules = this.rules( element );
			var rules = this.rulesCache[ element.name ];
			for( var i = 0, rule; rule = rules[i++]; ) {
				try {
					var result = jQuery.validator.methods[rule.method].call( this, jQuery.trim(element.value), element, rule.parameters );
					if( result === -1 )
						break;
					if( !result ) {
						jQuery( element ).addClass( this.settings.errorClass );
						this.formatAndAdd( rule, element);
						return false;
					}
				} catch(e) {
					this.settings.debug && window.console && console.error("exception occured when checking element " + element.id
						 + ", check the '" + rule.method + "' method");
					throw e;
				}
			}
			// show the label for valid elements if success callback is configured
			if ( rules.length && this.settings.success )
				this.successList.push(element);
			return true;
		},
		
		message: function( id, method ) {
			var m = this.settings.messages[id];
			return m && (m.constructor == String
				? m
				: m[method]);
		},
		
		formatAndAdd: function( rule, element) {
			var message = 
				this.message(element.name, rule.method)
				|| element.title
				|| jQuery.validator.messages[rule.method]
				|| "<strong>Warning: No message defined for " + element.name + "</strong>";
			if ( typeof message == "function" ) 
				message = message.call(this, rule.parameters, element);
			this.errorList.push({
				message: message,
				element: element
			});
			this.errorMap[element.name] = message;
			this.submitted[element.name] = message;
		},
		
		addWrapper: function(toToggle) {
			if ( this.settings.wrapper )
				toToggle.push( toToggle.parents( this.settings.wrapper ) );
			return toToggle;
		},
		
		defaultShowErrors: function() {
			for ( var i = 0, error; error = this.errorList[i]; i++ ) {
				this.showLabel( error.element, error.message );
			}
			if( this.errorList.length ) {
				this.toShow.push( this.containers );
			}
			for ( var i = 0, element; element = this.successList[i]; i++ ) {
				this.showLabel( element );
			}
			this.toHide = this.toHide.not( this.toShow );
			this.hideErrors();
			this.addWrapper( this.toShow ).show();
		},
		
		showLabel: function(element, message) {
			var label = this.errorsFor( element );
			if ( label.length ) {
				// refresh error/success class
				label.removeClass().addClass( this.settings.errorClass );
			
				// check if we have a generated label, replace the message then
				if( this.settings.overrideErrors || label.attr("generated") ) {
					label.html(message);
				}
			} else {
				// create label
				label = jQuery("<" + this.settings.errorElement + ">")
					.attr({"for":  this.idOrName(element), generated: true})
					.addClass(this.settings.errorClass)
					.html(message || "");
				if ( this.settings.wrapper ) {
					// make sure the element is visible, even in IE
					// actually showing the wrapped element is handled elsewhere
					label = label.hide().show().wrap("<" + this.settings.wrapper + ">").parent();
				}
				if ( !this.labelContainer.append(label).length )
					this.settings.errorPlacement
						? this.settings.errorPlacement(label, jQuery(element) )
						: label.insertAfter(element);
			}
			if ( !message && this.settings.success ) {
				label.text("");
				typeof this.settings.success == "string"
					? label.addClass( this.settings.success )
					: this.settings.success( label );
			}
			this.toShow.push(label);
		},
		
		errorsFor: function(element) {
			return this.errors().filter("[@for=" + this.idOrName(element) + "]");
		},
		
		idOrName: function(element) {
			return this.checkable(element) ? element.name : element.id || element.name;
		},

		rules: function( element ) {
			var data = this.data( element );
			if( !data )
				return [];
			var rules = [];
			// convert a simple string to a {string: true} rule, eg. "required" to {required:true}
			if( typeof data == "string" ) {
				var transformed = {};
				transformed[data] = true;
				data = transformed;
			}
			jQuery.each( data, function(key, value) {
				rules[rules.length] = {
					method: key,
					parameters: value
				};
			} );
			return rules;
		},

		data: function( element ) {
			return this.settings.rules
				? this.settings.rules[ element.name ]
				: this.settings.meta
					? jQuery(element).data()[ this.settings.meta ]
					: jQuery(element).data();
		},
		
		checkable: function( element ) {
			return /radio|checkbox/i.test(element.type);
		},
		
		checkableGroup: function( element ) {
			return jQuery(element.form || document).find('[@name="' + element.name + '"]');
		},
		
		getLength: function(value, element) {
			switch( element.nodeName.toLowerCase() ) {
			case 'select':
				return jQuery("option:selected", element).length;
			case 'input':
				if( this.checkable( element) )
					return this.checkableGroup( element).filter(':checked').length;
			}
			return value.length;
		},
	
		depend: function(param, element) {
			return this.dependTypes[typeof param]
				? this.dependTypes[typeof param](param, element)
				: true;
		},
	
		dependTypes: {
			"boolean": function(param, element) {
				return param;
			},
			"string": function(param, element) {
				return !!jQuery(param, element.form).length;
			},
			"function": function(param, element) {
				return param(element);
			}
		},
		
		required: function(element) {
			return !jQuery.validator.methods.required.call(this, jQuery.trim(element.value), element);
		}
		
	},

	/**
	 * Defines a standard set of useful validation methods.
	 * 
	 * Use jQuery.validator.addMethod() to add your own methods.
	 *
	 * If "all kind of text inputs" is mentioned for any if the methods defined here,
	 * it refers to input elements of type text, password and file and textareas.
	 *
	 * @param String value The trimmed value of the element, eg. the text of a text input (trimmed: whitespace removed at start and end)
	 * @param Element element the input element itself, to check for content of attributes other then value
	 * @param Object paramater Some parameter, like a number for min/max rules
	 *
	 * @property
	 * @name jQuery.validator.methods
	 * @type Object<String, Function(String,Element,Object):Boolean>
	 * @cat Plugins/Validate/Methods
	 */
	methods: {

		/**
		 * Return false if the element is empty.
		 *
		 * Works with all kind of text inputs, selects, checkboxes and radio buttons.
		 *
		 * To force a user to select an option from a select box, provide
		 * an empty options like <option value="">Choose...</option>
		 *
		 * @example <input name="firstname" class="{required:true}" />
		 * @desc Declares an input element that is required.
		 *
		 * @example <input id="other" type="radio" />
		 * <input name="details" class="{required:'input[@name=other]:checked'}" />
		 * @desc Declares an input element required, but only if a checkbox with name 'other' is checked.
		 * In other words: As long 'other' isn't checked, the details field is valid.
		 * Note: The expression is evaluated in the context of the current form. 
		 *
		 * @example jQuery("#myform").validate({
		 * 	rules: {
		 * 		details: {
		 * 			required: function(element) {
		 *				return jQuery("#other").is(":checked") && jQuery("#other2").is(":checked");
		 *			}
		 *		}
		 * 	}
		 * });
		 * @before <form id="myform">
		 * 	<input id="other" type="checkbox" />
		 * 	<input id="other2" type="checkbox" />
		 * 	<input name="details" />
		 * </form>
		 * @desc Declares an input element "details" required, but only if two other fields
		 * are checked.
		 *
		 * @example <fieldset>
		 * 	<legend>Family</legend>
		 * 	<label for="family_single">
		 * 		<input  type="radio" id="family_single" value="s" name="family" validate="required:true" />
		 * 		Single
		 * 	</label>
		 * 	<label for="family_married">
		 * 		<input  type="radio" id="family_married" value="m" name="family" />
		 * 		Married
		 * 	</label>
		 * 	<label for="family_divorced">
		 * 		<input  type="radio" id="family_divorced" value="d" name="family" />
		 * 		Divorced
		 * 	</label>
		 * 	<label for="family" class="error">Please select your family status.</label>
		 * </fieldset>
		 * @desc Specifies a group of radio elements. The validation rule is specified only for the first
		 * element of the group.
		 *
		 * @param String value The value of the element to check
		 * @param Element element The element to check
		 * @param Boolean|String|Function param A boolean "true" makes a field always required; An expression (String)
		 * is evaluated in the context of the element's form, making the field required only if the expression returns
		 * more then one element. The function is executed with the element as it's only argument: If it returns true,
		 * the element is required.
		 *
		 * @name jQuery.validator.methods.required
		 * @type Boolean
		 * @cat Plugins/Validate/Methods
		 */
		required: function(value, element, param) {
			// check if dependency is met
			if ( !this.depend(param, element) )
				return -1;
			switch( element.nodeName.toLowerCase() ) {
			case 'select':
				var options = jQuery("option:selected", element);
				return options.length > 0 && ( element.type == "select-multiple" || (jQuery.browser.msie && !(options[0].attributes['value'].specified) ? options[0].text : options[0].value).length > 0);
			case 'input':
				if ( this.checkable(element) )
					return this.getLength(value, element) > 0;
			default:
				return value.length > 0;
			}
		},

		/**
		 * Return false, if the element is
		 *
		 * - some kind of text input and its value is too short
		 *
		 * - a set of checkboxes has not enough boxes checked
		 *
		 * - a select and has not enough options selected
		 *
		 * Works with all kind of text inputs, checkboxes and select.
		 *
		 * @example <input name="firstname" class="{minLength:5}" />
		 * @desc Declares an optional input element with at least 5 characters (or none at all).
		 *
		 * @example <input name="firstname" class="{required:true,minLength:5}" />
		 * @desc Declares an input element that must have at least 5 characters.
		 *
		 * @example <fieldset>
		 * 	<legend>Spam</legend>
		 * 	<label for="spam_email">
		 * 		<input type="checkbox" id="spam_email" value="email" name="spam" validate="required:true,minLength:2" />
		 * 		Spam via E-Mail
		 * 	</label>
		 * 	<label for="spam_phone">
		 * 		<input type="checkbox" id="spam_phone" value="phone" name="spam" />
		 * 		Spam via Phone
		 * 	</label>
		 * 	<label for="spam_mail">
		 * 		<input type="checkbox" id="spam_mail" value="mail" name="spam" />
		 * 		Spam via Mail
		 * 	</label>
		 * 	<label for="spam" class="error">Please select at least two types of spam.</label>
		 * </fieldset>
		 * @desc Specifies a group of checkboxes. To validate, at least two checkboxes must be selected.
		 *
		 * @param Number min
		 * @name jQuery.validator.methods.minLength
		 * @type Boolean
		 * @cat Plugins/Validate/Methods
		 */
		minLength: function(value, element, param) {
			return this.required(element) || this.getLength(value, element) >= param;
		},
	
		/**
		 * Return false, if the element is
		 *
		 * - some kind of text input and its value is too big
		 *
		 * - a set of checkboxes has too many boxes checked
		 *
		 * - a select and has too many options selected
		 *
		 * Works with all kind of text inputs, checkboxes and selects.
		 *
		 * @example <input name="firstname" class="{maxLength:5}" />
		 * @desc Declares an input element with at most 5 characters.
		 *
		 * @example <input name="firstname" class="{required:true,maxLength:5}" />
		 * @desc Declares an input element that must have at least one and at most 5 characters.
		 *
		 * @param Number max
		 * @name jQuery.validator.methods.maxLength
		 * @type Boolean
		 * @cat Plugins/Validate/Methods
		 */
		maxLength: function(value, element, param) {
			return this.required(element) || this.getLength(value, element) <= param;
		},
		
		/**
		 * Return false, if the element is
		 *
	     * - some kind of text input and its value is too short or too long
	     *
	     * - a set of checkboxes has not enough or too many boxes checked
	     *
	     * - a select and has not enough or too many options selected
	     *
	     * Works with all kind of text inputs, checkboxes and selects.
	     *
		 * @example <input name="firstname" class="{rangeLength:[3,5]}" />
		 * @desc Declares an optional input element with at least 3 and at most 5 characters (or none at all).
		 *
		 * @example <input name="firstname" class="{required:true,rangeLength:[3,5]}" />
		 * @desc Declares an input element that must have at least 3 and at most 5 characters.
		 *
		 * @example <select id="cars" class="{required:true,rangeLength:[2,3]}" multiple="multiple">
		 * 	<option value="m_sl">Mercedes SL</option>
		 * 	<option value="o_c">Opel Corsa</option>
		 * 	<option value="vw_p">VW Polo</option>
		 * 	<option value="t_s">Titanic Skoda</option>
		 * </select>
		 * @desc Specifies a select that must have at least two but no more then three options selected.
		 *
	     * @param Array<Number> min/max
	     * @name jQuery.validator.methods.rangeLength
	     * @type Boolean
	     * @cat Plugins/Validate/Methods
	     */
		rangeLength: function(value, element, param) {
			var length = this.getLength(value, element);
			return this.required(element) || ( length >= param[0] && length <= param[1] );
		},
	
		/**
		 * Return true, if the value is greater than or equal to the specified minimum.
		 *
		 * Works with all kind of text inputs.
		 *
		 * @example <input name="age" class="{minValue:16}" />
		 * @desc Declares an optional input element whose value must be at least 16 (or none at all).
		 *
		 * @example <input name="age" class="{required:true,minValue:16}" />
		 * @desc Declares an input element whose value must be at least 16.
		 *
		 * @param Number min
		 * @name jQuery.validator.methods.minValue
		 * @type Boolean
		 * @cat Plugins/Validate/Methods
		 */
		minValue: function( value, element, param ) {
			return this.required(element) || value >= param;
		},
		
		/**
		 * Return true, if the value is less than or equal to the specified maximum.
		 *
		 * Works with all kind of text inputs.
		 *
		 * @example <input name="age" class="{maxValue:16}" />
		 * @desc Declares an optional input element whose value must be at most 16 (or none at all).
		 *
		 * @example <input name="age" class="{required:true,maxValue:16}" />
		 * @desc Declares an input element whose required value must be at most 16.
		 *
		 * @param Number max
		 * @name jQuery.validator.methods.maxValue
		 * @type Boolean
		 * @cat Plugins/Validate/Methods
		 */
		maxValue: function( value, element, param ) {
			return this.required(element) || value <= param;
		},
		
		/**
		 * Return true, if the value is in the specified range.
		 *
		 * Works with all kind of text inputs.
		 *
		 * @example <input name="age" class="{rangeValue:[4,12]}" />
		 * @desc Declares an optional input element whose value must be at least 4 and at most 12 (or none at all).
		 *
		 * @example <input name="age" class="{required:true,rangeValue:[4,12]}" />
		 * @desc Declares an input element whose required value must be at least 4 and at most 12.
		 *
		 * @param Array<Number> min/max
		 * @name jQuery.validator.methods.rangeValue
		 * @type Boolean
		 * @cat Plugins/Validate/Methods
		 */
		rangeValue: function( value, element, param ) {
			return this.required(element) || ( value >= param[0] && value <= param[1] );
		},
		
		/**
		 * Return true, if the value is not a valid email address.
		 *
		 * Works with all kind of text inputs.
		 *
		 * @example <input name="email1" class="{email:true}" />
		 * @desc Declares an optional input element whose value must be a valid email address (or none at all).
		 *
		 * @example <input name="email1" class="{required:true,email:true}" />
		 * @desc Declares an input element whose value must be a valid email address.
		 *
		 * @name jQuery.validator.methods.email
		 * @type Boolean
		 * @cat Plugins/Validate/Methods
		 */
		email: function(value, element) {
			return this.required(element) || /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/i.test(value);
		},
	
		/**
		 * Return true, if the value is a valid url.
		 *
		 * Works with all kind of text inputs.
		 *
		 * See http://www.w3.org/Addressing/rfc1738.txt for URL specification.
		 *
		 * @example <input name="homepage" class="{url:true}" />
		 * @desc Declares an optional input element whose value must be a valid URL (or none at all).
		 *
		 * @example <input name="homepage" class="{required:true,url:true}" />
		 * @desc Declares an input element whose value must be a valid URL.
		 *
		 * @name jQuery.validator.methods.url
		 * @type Boolean
		 * @cat Plugins/Validate/Methods
		 */
		url: function(value, element) {
			return this.required(element) || /^(https?|ftp):\/\/[A-Z0-9](\.?[A-Z0-9ÄÜÖ][A-Z0-9_\-ÄÜÖ]*)*(\/([A-Z0-9ÄÜÖ][A-Z0-9_\-\.ÄÜÖ]*)?)*(\?([A-Z0-9ÄÜÖ][A-Z0-9_\-\.%\+=&ÄÜÖ]*)?)?$/i.test(value);
		},
        
		/**
		 * Return true, if the value is a valid date. Uses JavaScripts built-in
		 * Date to test if the date is valid, and is therefore very limited.
		 *
		 * Works with all kind of text inputs.
		 *
		 * @example <input name="birthdate" class="{date:true}" />
		 * @desc Declares an optional input element whose value must be a valid date (or none at all).
		 *
		 * @example <input name="birthdate" class="{required:true,date:true}" />
		 * @desc Declares an input element whose value must be a valid date.
		 *
		 * @name jQuery.validator.methods.date
		 * @type Boolean
		 * @cat Plugins/Validate/Methods
		 */
		date: function(value, element) {
			return this.required(element) || !/Invalid|NaN/.test(new Date(value));
		},
	
		/**
		 * Return true, if the value is a valid date, according to ISO date standard.
		 *
		 * Works with all kind of text inputs.
		 *
		 * @example jQuery.validator.methods.date("1990/01/01")
		 * @result true
		 *
		 * @example jQuery.validator.methods.date("1990-01-01")
		 * @result true
		 *
		 * @example jQuery.validator.methods.date("01.01.1990")
		 * @result false
		 *
		 * @example <input name="birthdate" class="{dateISO:true}" />
		 * @desc Declares an optional input element whose value must be a valid ISO date (or none at all).
		 *
		 * @name jQuery.validator.methods.date
		 * @type Boolean
		 * @cat Plugins/Validate/Methods
		 */
		dateISO: function(value, element) {
			return this.required(element) || /^\d{4}[\/-]\d{1,2}[\/-]\d{1,2}$/.test(value);
		},
	
		/**
		 * Return true, if the value is a valid date. Supports german
		 * dates (29.04.1994 or 1.1.2006). Doesn't make any sanity checks.
		 *
		 * Works with all kind of text inputs.
		 *
		 * @example jQuery.validator.methods.date("1990/01/01")
		 * @result false
		 *
		 * @example jQuery.validator.methods.date("01.01.1990")
		 * @result true
		 *
		 * @example jQuery.validator.methods.date("0.1.2345")
		 * @result true
		 *
		 * @example <input name="geburtstag" class="{dateDE:true}" />
		 * @desc Declares an optional input element whose value must be a valid german date (or none at all).
		 *
		 * @name jQuery.validator.methods.dateDE
		 * @type Boolean
		 * @cat Plugins/Validate/Methods
		 */
		dateDE: function(value, element) {
			return this.required(element) || /^\d\d?\.\d\d?\.\d\d\d?\d?$/.test(value);
		},
	
		/**
		 * Return true, if the value is a valid number. Checks for
		 * international number format, eg. 100,000.59
		 *
		 * Works with all kind of text inputs.
		 *
		 * @example <input name="amount" class="{number:true}" />
		 * @desc Declares an optional input element whose value must be a valid number (or none at all).
		 *
		 * @name jQuery.validator.methods.number
		 * @type Boolean
		 * @cat Plugins/Validate/Methods
		 */
		number: function(value, element) {
			return this.required(element) || /^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/.test(value);
		},
	
		/**
		 * Return true, if the value is a valid number.
		 *
		 * Works with all kind of text inputs.
		 *
		 * Checks for german numbers (100.000,59)
		 *
		 * @example <input name="menge" class="{numberDE:true}" />
		 * @desc Declares an optional input element whose value must be a valid german number (or none at all).
		 *
		 * @name jQuery.validator.methods.numberDE
		 * @type Boolean
		 * @cat Plugins/Validate/Methods
		 */
		numberDE: function(value, element) {
			return this.required(element) || /^-?(?:\d+|\d{1,3}(?:\.\d{3})+)(?:,\d+)?$/.test(value);
		},
		
		/**
		 * Returns true if the value contains only digits.
		 *
		 * Works with all kind of text inputs.
		 *
		 * @example <input name="serialnumber" class="{digits:true}" />
		 * @desc Declares an optional input element whose value must contain only digits (or none at all).
		 *
		 * @name jQuery.validator.methods.digits
		 * @type Boolean
		 * @cat Plugins/Validate/Methods
		 */
		digits: function(value, element) {
			return this.required(element) || /^\d+$/.test(value);
		},
		
		 /**
         * Return true, if the value is a valid credit card number.
         *
         * Works with all kind of text inputs.
         *
         * @example <input name="cc1" class="{required:true,creditcard:true}" />
         * @desc Declares a required input element whose value must be a valid credit card number (ignoring any non-digts).
         *
         * @example <input name="cc2" class="{required:true,digits:true,creditcard:true}" />
         * @desc Declares a required input element whose value must be a valid credit card number.  No spaces or dashes allowed.
         *
         * @name jQuery.validator.methods.creditcard
         * @type Boolean
         * @cat Plugins/Validate/Methods
         */
		creditcard: function(value, element) {
			if ( this.required(element) )
				return true;
			var nCheck = 0,
				nDigit = 0,
				bEven = false;

			value = value.replace(/\D/g, "");

			for (n = value.length - 1; n >= 0; n--) {
				var cDigit = value.charAt(n);
				var nDigit = parseInt(cDigit, 10);
				if (bEven) {
					if ((nDigit *= 2) > 9)
						nDigit -= 9;
				}
				nCheck += nDigit;
				bEven = !bEven;
			}

			return (nCheck % 10) == 0;
		},
		
		/**
		 * Returns true if the value ends with one of the specified file extensions.
		 * If nothing is specified, only images are allowed (default-param: "png|jpe?g|gif").
		 *
		 * Works with all kind of text inputs.
		 *
		 * @example <input type="file" name="avatar" class="{accept:true}" />
		 * @desc Declares an optional file input element whose value must ends with '.png', '.jpg', '.jpeg' or '.gif'.
		 *
		 * @example <input type="file" name="avatar" class="{accept:'txt|docx?'}" />
		 * @desc Declares an optional file input element whose value must ends with '.txt' or '.doc' or '.docx'.
		 *
		 * @name jQuery.validator.methods.accept
		 * @type Boolean
		 * @cat Plugins/Validate/Methods
		 */
		accept: function(value, element, param) {
			param = typeof param == "string" ? param : "png|jpe?g|gif";
			return this.required(element) || value.match(new RegExp(".(" + param + ")$")); 
		},
		
		/**
		 * Returns true if the value has the same value
		 * as the element specified by the first parameter.
		 *
		 * Keep the expression simple to avoid spaces when using metadata.
		 *
		 * Works with all kind of text inputs.
		 *
		 * @example <input name="email" id="email" class="{required:true,email:true'}" />
		 * <input name="emailAgain" class="{equalTo:'#email'}" />
		 * @desc Declares two input elements: The first must contain a valid email address,
		 * the second must contain the same adress, enter once more. The paramter is a
		 * expression used via jQuery to select the element.
		 *
		 * @param String selection A jQuery expression
		 * @name jQuery.validator.methods.digits
		 * @type Boolean
		 * @cat Plugins/Validate/Methods
		 */
		equalTo: function(value, element, param) {
			return value == jQuery(param).val();
		}
		
	},
	
	/**
	 * Add a new validation method. It must consist of a name (must be a legal
	 * javascript identifier), a function and a default message.
	 *
	 * Please note: While the temptation is great to
	 * add a regex method that checks it's paramter against the value,
	 * it is much cleaner to encapsulate those regular expressions
	 * inside their own method. If you need lots of slightly different
	 * expressions, try to extract a common parameter.
	 *
	 * A library of regular expressions: http://regexlib.com/DisplayPatterns.aspx
	 *
	 * @example jQuery.validator.addMethod("domain", function(value) {
	 *   return /^http://mycorporatedomain.com/.test(value);
	 * }, "Please specify the correct domain for your documents");
	 * @desc Adds a method that checks if the value starts with http://mycorporatedomain.com
	 *
	 * @example jQuery.validator.addMethod("math", function(value, element, params) {
	 *  return this.required(value, element) || value == params[0] + params[1];
	 * }, "Please enter the correct value for this simple question.");
	 * @desc Adds a not-required method...
	 *
	 * @see jQuery.validator.methods
	 *
	 * @param String name The name of the method, used to identify and referencing it, must be a valid javascript identifier
	 * @param Function rule The actual method implementation, returning true if an element is valid
	 * @param String message The default message to display for this method
	 *
	 * @name jQuery.validator.addMethod
	 * @type undefined
	 * @cat Plugins/Validate
	 */
	addMethod: function(name, method, message) {
		jQuery.validator.methods[name] = method;
		jQuery.validator.messages[name] = message;
	}
});
