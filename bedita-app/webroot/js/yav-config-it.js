/***********************************************************************
 * YAV - Yet Another Validator  v1.3.0                                 *
 * Copyright (C) 2005-2006                                             *
 * Author: Federico Crivellaro <f.crivellaro@gmail.com>                *
 * WWW: http://yav.sourceforge.net                                     *
 ***********************************************************************/

// CHANGE THESE VARIABLES FOR YOUR OWN SETUP

// if you want yav to highligh fields with errors
inputhighlight = true;
// classname you want for the error highlighting
inputclasserror = 'inputError';
// classname you want for your fields without highlighting
inputclassnormal = 'inputNormal';
// classname you want for the inner html highlighting
innererror = 'innerError';
// div name where errors will appear (or where jsVar variable is dinamically defined)
errorsdiv = 'errorsDiv';
// if you want yav to alert you for javascript errors (only for developers)
debugmode = false;
// if you want yav to trim the strings
trimenabled = true;

// change these to set your own decimal separator and your date format
DECIMAL_SEP =',';
THOUSAND_SEP = '.';
DATE_FORMAT = 'dd-MM-yyyy';

// change these strings for your own translation (do not change {n} values!)
HEADER_MSG = 'Dati non validi:';
FOOTER_MSG = 'Correggi e riprova.';
DEFAULT_MSG = 'I dati non sono validi.';
REQUIRED_MSG = 'Inserisci {1}.';
ALPHABETIC_MSG = '{1} non valido. Caratteri ammessi: A-Za-z';
ALPHANUMERIC_MSG = '{1} non valido. Caratteri ammessi: A-Za-z0-9';
ALNUMHYPHEN_MSG = '{1} non valido. Caratteri ammessi: A-Za-z0-9\-_';
ALNUMHYPHENAT_MSG = '{1} non valido. Caratteri ammessi: A-Za-z0-9\-_@';
ALPHASPACE_MSG = '{1} non valido. Caratteri ammessi: A-Za-z0-9\-_space';
MINLENGTH_MSG = '{1} deve essere lungo almeno {2} caratteri.';
MAXLENGTH_MSG = '{1} non deve essere lungo al massimo {2} caratteri.';
NUMRANGE_MSG = '{1} deve essere un numero in {2}.';
DATE_MSG = '{1} data non valida secondo il formato ' + DATE_FORMAT + '.';
NUMERIC_MSG = '{1} deve essere un numero.';
INTEGER_MSG = '{1} deve essere un intero';
DOUBLE_MSG = '{1} deve essere un numero decimale.';
REGEXP_MSG = '{1} non valido. Formato ammesso: {2}.';
EQUAL_MSG = '{1} deve essere uguale a {2}.';
NOTEQUAL_MSG = '{1} non deve essere uguale a {2}.';
DATE_LT_MSG = '{1} deve essere precedente a {2}.';
DATE_LE_MSG = '{1} deve essere non successiva a {2}.';
EMAIL_MSG = '{1} deve essere una e-mail valida.';
EMPTY_MSG = '{1} deve essere vuoto.';
