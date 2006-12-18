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
DECIMAL_SEP =' ';
THOUSAND_SEP = ',';
DATE_FORMAT = 'dd.MM.yyyy';

// change these strings for your own translation (do not change {n} values!)
HEADER_MSG = 'Neplatné dáta:';
FOOTER_MSG = 'Skúste znova.';
DEFAULT_MSG = 'Dáta sú neplatné.';
REQUIRED_MSG = 'Zadaj {1}.';
ALPHABETIC_MSG = '{1} je neplatné. Povolené znaky: A-Za-z';
ALPHANUMERIC_MSG = '{1} je neplatné. Povolené znaky: A-Za-z0-9';
ALNUMHYPHEN_MSG = '{1} je neplatné. Povolené znaky: A-Za-z0-9\-_';
ALNUMHYPHENAT_MSG = '{1} je neplatné. Povolené znaky: A-Za-z0-9\-_@';
ALPHASPACE_MSG = '{1} je neplatné. Povolené znaky: A-Za-z0-9\-_space';
MINLENGTH_MSG = '{1} musí obsahova aspoò {2} znaky/ov.';
MAXLENGTH_MSG = '{1} musí obsahova aspoò {2} znaky/ov.';
NUMRANGE_MSG = '{1} musí by èíslo v rozsahu {2}.';
DATE_MSG = '{1} nie je platnı dátum, poui formát ' + DATE_FORMAT + '.';
NUMERIC_MSG = '{1} musí by èíslo.';
INTEGER_MSG = '{1} musí by reálne.';
DOUBLE_MSG = '{1} musí by desatinné .';
REGEXP_MSG = '{1} nie je platné. Povolenı formát: {2}.';
EQUAL_MSG = '{1} musí by ekvivalentné s {2}.';
NOTEQUAL_MSG = '{1}musí by ekvivalentné s {2}.';
DATE_LT_MSG = '{1} musí by predchádzajúce {2}.';
DATE_LE_MSG = '{1} musí by predchádzajúce alebo ekvivalentné s {2}.';
EMAIL_MSG = '{1} musí by platnım e-mailom.';
EMPTY_MSG = '{1} musí by prázdne.';
