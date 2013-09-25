<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Milos Jancovic
 *   @since Version 1.0.0
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Country_Countries extends Gpf_Object {

	public static function getEncodedCountries() {
		$countries = self::createCountries();
		$output = new Gpf_Rpc_Array();
		
		$output->add(new Gpf_Rpc_Array(array('code', 'name')));
		foreach ($countries as $key => $country) {
			$output->add(new Gpf_Rpc_Array(array($key, $country)));
		}		
		return $output;
	}
	
    private static $countries = null;

    public static function getCountryName($code) {
    	$countries = self::createCountries();
    	if (array_key_exists($code, $countries)) {
    		return $countries[$code];
    	}
    	return "";
    }
    
    private static function createCountries(){
        $countries = array();
        $countries['AP'] = Gpf_Lang::_runtime('Asia/PacificRegion');
        $countries['EU'] = Gpf_Lang::_runtime('Europe');
        $countries['AD'] = Gpf_Lang::_runtime('Andorra');
        $countries['AE'] = Gpf_Lang::_runtime('United Arab Emirates');
        $countries['AF'] = Gpf_Lang::_runtime('Afghanistan');
        $countries['AG'] = Gpf_Lang::_runtime('Antigua and Barbuda');
        $countries['AI'] = Gpf_Lang::_runtime('Anguilla');
        $countries['AL'] = Gpf_Lang::_runtime('Albania');
        $countries['AM'] = Gpf_Lang::_runtime('Armenia');
        $countries['AN'] = Gpf_Lang::_runtime('Netherlands Antilles');
        $countries['AO'] = Gpf_Lang::_runtime('Angola');
        $countries['AQ'] = Gpf_Lang::_runtime('Antarctica');
        $countries['AR'] = Gpf_Lang::_runtime('Argentina');
        $countries['AS'] = Gpf_Lang::_runtime('American Samoa');
        $countries['AT'] = Gpf_Lang::_runtime('Austria');
        $countries['AU'] = Gpf_Lang::_runtime('Australia');
        $countries['AW'] = Gpf_Lang::_runtime('Aruba');
        $countries['AZ'] = Gpf_Lang::_runtime('Azerbaijan');
        $countries['BA'] = Gpf_Lang::_runtime('Bosnia and Herzegovina');
        $countries['BB'] = Gpf_Lang::_runtime('Barbados');
        $countries['BD'] = Gpf_Lang::_runtime('Bangladesh');
        $countries['BE'] = Gpf_Lang::_runtime('Belgium');
        $countries['BF'] = Gpf_Lang::_runtime('Burkina Faso');
        $countries['BG'] = Gpf_Lang::_runtime('Bulgaria');
        $countries['BH'] = Gpf_Lang::_runtime('Bahrain');
        $countries['BI'] = Gpf_Lang::_runtime('Burundi');
        $countries['BJ'] = Gpf_Lang::_runtime('Benin');
        $countries['BM'] = Gpf_Lang::_runtime('Bermuda');
        $countries['BN'] = Gpf_Lang::_runtime('Brunei Darussalam');
        $countries['BO'] = Gpf_Lang::_runtime('Bolivia');
        $countries['BR'] = Gpf_Lang::_runtime('Brazil');
        $countries['BS'] = Gpf_Lang::_runtime('Bahamas');
        $countries['BT'] = Gpf_Lang::_runtime('Bhutan');
        $countries['BV'] = Gpf_Lang::_runtime('Bouvet Island');
        $countries['BW'] = Gpf_Lang::_runtime('Botswana');
        $countries['BY'] = Gpf_Lang::_runtime('Belarus');
        $countries['BZ'] = Gpf_Lang::_runtime('Belize');
        $countries['CA'] = Gpf_Lang::_runtime('Canada');
        $countries['CC'] = Gpf_Lang::_runtime('Cocos (Keeling) Islands');
        $countries['CD'] = Gpf_Lang::_runtime('Congo, The Democratic Republic of the');
        $countries['CF'] = Gpf_Lang::_runtime('Central African Republic');
        $countries['CG'] = Gpf_Lang::_runtime('Congo');
        $countries['CH'] = Gpf_Lang::_runtime('Switzerland');
        $countries['CI'] = Gpf_Lang::_runtime('Cote D\'Ivoire');
        $countries['CK'] = Gpf_Lang::_runtime('Cook Islands');
        $countries['CL'] = Gpf_Lang::_runtime('Chile');
        $countries['CM'] = Gpf_Lang::_runtime('Cameroon');
        $countries['CN'] = Gpf_Lang::_runtime('China');
        $countries['CO'] = Gpf_Lang::_runtime('Colombia');
        $countries['CR'] = Gpf_Lang::_runtime('Costa Rica');
        $countries['CU'] = Gpf_Lang::_runtime('Cuba');
        $countries['CV'] = Gpf_Lang::_runtime('Cape Verde');
        $countries['CX'] = Gpf_Lang::_runtime('Christmas Island');
        $countries['CY'] = Gpf_Lang::_runtime('Cyprus');
        $countries['CZ'] = Gpf_Lang::_runtime('Czech Republic');
        $countries['DE'] = Gpf_Lang::_runtime('Germany');
        $countries['DJ'] = Gpf_Lang::_runtime('Djibouti');
        $countries['DK'] = Gpf_Lang::_runtime('Denmark');
        $countries['DM'] = Gpf_Lang::_runtime('Dominica');
        $countries['DO'] = Gpf_Lang::_runtime('Dominican Republic');
        $countries['DZ'] = Gpf_Lang::_runtime('Algeria');
        $countries['EC'] = Gpf_Lang::_runtime('Ecuador');
        $countries['EE'] = Gpf_Lang::_runtime('Estonia');
        $countries['EG'] = Gpf_Lang::_runtime('Egypt');
        $countries['EH'] = Gpf_Lang::_runtime('Western Sahara');
        $countries['ER'] = Gpf_Lang::_runtime('Eritrea');
        $countries['ES'] = Gpf_Lang::_runtime('Spain');
        $countries['ET'] = Gpf_Lang::_runtime('Ethiopia');
        $countries['FI'] = Gpf_Lang::_runtime('Finland');
        $countries['FJ'] = Gpf_Lang::_runtime('Fiji');
        $countries['FK'] = Gpf_Lang::_runtime('Falkland Islands (Malvinas)');
        $countries['FM'] = Gpf_Lang::_runtime('Micronesia, Federated States of');
        $countries['FO'] = Gpf_Lang::_runtime('Faroe Islands');
        $countries['FR'] = Gpf_Lang::_runtime('France');
        $countries['FX'] = Gpf_Lang::_runtime('France, Metropolitan');
        $countries['GA'] = Gpf_Lang::_runtime('Gabon');
        $countries['GB'] = Gpf_Lang::_runtime('United Kingdom');
        $countries['GD'] = Gpf_Lang::_runtime('Grenada');
        $countries['GE'] = Gpf_Lang::_runtime('Georgia');
        $countries['GF'] = Gpf_Lang::_runtime('French Guiana');
        $countries['GH'] = Gpf_Lang::_runtime('Ghana');
        $countries['GI'] = Gpf_Lang::_runtime('Gibraltar');
        $countries['GL'] = Gpf_Lang::_runtime('Greenland');
        $countries['GM'] = Gpf_Lang::_runtime('Gambia');
        $countries['GN'] = Gpf_Lang::_runtime('Guinea');
        $countries['GP'] = Gpf_Lang::_runtime('Guadeloupe');
        $countries['GQ'] = Gpf_Lang::_runtime('Equatorial Guinea');
        $countries['GR'] = Gpf_Lang::_runtime('Greece');
        $countries['GS'] = Gpf_Lang::_runtime('South Georgia and the South Sandwich Islands');
        $countries['GT'] = Gpf_Lang::_runtime('Guatemala');
        $countries['GU'] = Gpf_Lang::_runtime('Guam');
        $countries['GW'] = Gpf_Lang::_runtime('Guinea-Bissau');
        $countries['GY'] = Gpf_Lang::_runtime('Guyana');
        $countries['HK'] = Gpf_Lang::_runtime('Hong Kong');
        $countries['HM'] = Gpf_Lang::_runtime('Heard Island and McDonald Islands');
        $countries['HN'] = Gpf_Lang::_runtime('Honduras');
        $countries['HR'] = Gpf_Lang::_runtime('Croatia');
        $countries['HT'] = Gpf_Lang::_runtime('Haiti');
        $countries['HU'] = Gpf_Lang::_runtime('Hungary');
        $countries['ID'] = Gpf_Lang::_runtime('Indonesia');
        $countries['IE'] = Gpf_Lang::_runtime('Ireland');
        $countries['IL'] = Gpf_Lang::_runtime('Israel');
        $countries['IN'] = Gpf_Lang::_runtime('India');
        $countries['IO'] = Gpf_Lang::_runtime('British Indian Ocean Territory');
        $countries['IQ'] = Gpf_Lang::_runtime('Iraq');
        $countries['IR'] = Gpf_Lang::_runtime('Iran, Islamic Republic of');
        $countries['IS'] = Gpf_Lang::_runtime('Iceland');
        $countries['IT'] = Gpf_Lang::_runtime('Italy');
        $countries['JM'] = Gpf_Lang::_runtime('Jamaica');
        $countries['JO'] = Gpf_Lang::_runtime('Jordan');
        $countries['JP'] = Gpf_Lang::_runtime('Japan');
        $countries['KE'] = Gpf_Lang::_runtime('Kenya');
        $countries['KG'] = Gpf_Lang::_runtime('Kyrgyzstan');
        $countries['KH'] = Gpf_Lang::_runtime('Cambodia');
        $countries['KI'] = Gpf_Lang::_runtime('Kiribati');
        $countries['KM'] = Gpf_Lang::_runtime('Comoros');
        $countries['KN'] = Gpf_Lang::_runtime('Saint Kitts and Nevis');
        $countries['KP'] = Gpf_Lang::_runtime('Korea, Democratic People\'s Republic of');
        $countries['KR'] = Gpf_Lang::_runtime('Korea, Republic of');
        $countries['KW'] = Gpf_Lang::_runtime('Kuwait');
        $countries['KY'] = Gpf_Lang::_runtime('Cayman Islands');
        $countries['KZ'] = Gpf_Lang::_runtime('Kazakstan');
        $countries['LA'] = Gpf_Lang::_runtime('Lao People\'s Democratic Republic');
        $countries['LB'] = Gpf_Lang::_runtime('Lebanon');
        $countries['LC'] = Gpf_Lang::_runtime('Saint Lucia');
        $countries['LI'] = Gpf_Lang::_runtime('Liechtenstein');
        $countries['LK'] = Gpf_Lang::_runtime('SriLanka');
        $countries['LR'] = Gpf_Lang::_runtime('Liberia');
        $countries['LS'] = Gpf_Lang::_runtime('Lesotho');
        $countries['LT'] = Gpf_Lang::_runtime('Lithuania');
        $countries['LU'] = Gpf_Lang::_runtime('Luxembourg');
        $countries['LV'] = Gpf_Lang::_runtime('Latvia');
        $countries['LY'] = Gpf_Lang::_runtime('Libyan Arab Jamahiriya');
        $countries['MA'] = Gpf_Lang::_runtime('Morocco');
        $countries['MC'] = Gpf_Lang::_runtime('Monaco');
        $countries['MD'] = Gpf_Lang::_runtime('Moldova, Republic of');
        $countries['MG'] = Gpf_Lang::_runtime('Madagascar');
        $countries['MH'] = Gpf_Lang::_runtime('Marshall Islands');
        $countries['MK'] = Gpf_Lang::_runtime('Macedonia');
        $countries['ML'] = Gpf_Lang::_runtime('Mali');
        $countries['MM'] = Gpf_Lang::_runtime('Myanmar');
        $countries['MN'] = Gpf_Lang::_runtime('Mongolia');
        $countries['MO'] = Gpf_Lang::_runtime('Macau');
        $countries['MP'] = Gpf_Lang::_runtime('Northern Mariana Islands');
        $countries['MQ'] = Gpf_Lang::_runtime('Martinique');
        $countries['MR'] = Gpf_Lang::_runtime('Mauritania');
        $countries['MS'] = Gpf_Lang::_runtime('Montserrat');
        $countries['MT'] = Gpf_Lang::_runtime('Malta');
        $countries['MU'] = Gpf_Lang::_runtime('Mauritius');
        $countries['MV'] = Gpf_Lang::_runtime('Maldives');
        $countries['MW'] = Gpf_Lang::_runtime('Malawi');
        $countries['MX'] = Gpf_Lang::_runtime('Mexico');
        $countries['MY'] = Gpf_Lang::_runtime('Malaysia');
        $countries['MZ'] = Gpf_Lang::_runtime('Mozambique');
        $countries['NA'] = Gpf_Lang::_runtime('Namibia');
        $countries['NC'] = Gpf_Lang::_runtime('New Caledonia');
        $countries['NE'] = Gpf_Lang::_runtime('Niger');
        $countries['NF'] = Gpf_Lang::_runtime('Norfolk Island');
        $countries['NG'] = Gpf_Lang::_runtime('Nigeria');
        $countries['NI'] = Gpf_Lang::_runtime('Nicaragua');
        $countries['NL'] = Gpf_Lang::_runtime('Netherlands');
        $countries['NO'] = Gpf_Lang::_runtime('Norway');
        $countries['NP'] = Gpf_Lang::_runtime('Nepal');
        $countries['NR'] = Gpf_Lang::_runtime('Nauru');
        $countries['NU'] = Gpf_Lang::_runtime('Niue');
        $countries['NZ'] = Gpf_Lang::_runtime('New Zealand');
        $countries['OM'] = Gpf_Lang::_runtime('Oman');
        $countries['PA'] = Gpf_Lang::_runtime('Panama');
        $countries['PE'] = Gpf_Lang::_runtime('Peru');
        $countries['PF'] = Gpf_Lang::_runtime('French Polynesia');
        $countries['PG'] = Gpf_Lang::_runtime('Papua New Guinea');
        $countries['PH'] = Gpf_Lang::_runtime('Philippines');
        $countries['PK'] = Gpf_Lang::_runtime('Pakistan');
        $countries['PL'] = Gpf_Lang::_runtime('Poland');
        $countries['PM'] = Gpf_Lang::_runtime('Saint Pierre and Miquelon');
        $countries['PN'] = Gpf_Lang::_runtime('Pitcairn Islands');
        $countries['PR'] = Gpf_Lang::_runtime('Puerto Rico');
        $countries['PS'] = Gpf_Lang::_runtime('Palestinian Territory');
        $countries['PT'] = Gpf_Lang::_runtime('Portugal');
        $countries['PW'] = Gpf_Lang::_runtime('Palau');
        $countries['PY'] = Gpf_Lang::_runtime('Paraguay');
        $countries['QA'] = Gpf_Lang::_runtime('Qatar');
        $countries['RE'] = Gpf_Lang::_runtime('Reunion');
        $countries['RO'] = Gpf_Lang::_runtime('Romania');
        $countries['RU'] = Gpf_Lang::_runtime('Russian Federation');
        $countries['RW'] = Gpf_Lang::_runtime('Rwanda');
        $countries['SA'] = Gpf_Lang::_runtime('Saudi Arabia');
        $countries['SB'] = Gpf_Lang::_runtime('Solomon Islands');
        $countries['SC'] = Gpf_Lang::_runtime('Seychelles');
        $countries['SD'] = Gpf_Lang::_runtime('Sudan');
        $countries['SE'] = Gpf_Lang::_runtime('Sweden');
        $countries['SG'] = Gpf_Lang::_runtime('Singapore');
        $countries['SH'] = Gpf_Lang::_runtime('Saint Helena');
        $countries['SI'] = Gpf_Lang::_runtime('Slovenia');
        $countries['SJ'] = Gpf_Lang::_runtime('Svalbardand Jan Mayen');
        $countries['SK'] = Gpf_Lang::_runtime('Slovakia');
        $countries['SL'] = Gpf_Lang::_runtime('Sierra Leone');
        $countries['SM'] = Gpf_Lang::_runtime('San Marino');
        $countries['SN'] = Gpf_Lang::_runtime('Senegal');
        $countries['SO'] = Gpf_Lang::_runtime('Somalia');
        $countries['SR'] = Gpf_Lang::_runtime('Suriname');
        $countries['ST'] = Gpf_Lang::_runtime('Sao Tome and Principe');
        $countries['SV'] = Gpf_Lang::_runtime('El Salvador');
        $countries['SY'] = Gpf_Lang::_runtime('Syrian Arab Republic');
        $countries['SZ'] = Gpf_Lang::_runtime('Swaziland');
        $countries['TC'] = Gpf_Lang::_runtime('Turks and Caicos Islands');
        $countries['TD'] = Gpf_Lang::_runtime('Chad');
        $countries['TF'] = Gpf_Lang::_runtime('French Southern Territories');
        $countries['TG'] = Gpf_Lang::_runtime('Togo');
        $countries['TH'] = Gpf_Lang::_runtime('Thailand');
        $countries['TJ'] = Gpf_Lang::_runtime('Tajikistan');
        $countries['TK'] = Gpf_Lang::_runtime('Tokelau');
        $countries['TM'] = Gpf_Lang::_runtime('Turkmenistan');
        $countries['TN'] = Gpf_Lang::_runtime('Tunisia');
        $countries['TO'] = Gpf_Lang::_runtime('Tonga');
        $countries['TL'] = Gpf_Lang::_runtime('Timor-Leste');
        $countries['TR'] = Gpf_Lang::_runtime('Turkey');
        $countries['TT'] = Gpf_Lang::_runtime('Trinidad and Tobago');
        $countries['TV'] = Gpf_Lang::_runtime('Tuvalu');
        $countries['TW'] = Gpf_Lang::_runtime('Taiwan');
        $countries['TZ'] = Gpf_Lang::_runtime('Tanzania, United Republic of');
        $countries['UA'] = Gpf_Lang::_runtime('Ukraine');
        $countries['UG'] = Gpf_Lang::_runtime('Uganda');
        $countries['UM'] = Gpf_Lang::_runtime('United States Minor Outlying Islands');
        $countries['US'] = Gpf_Lang::_runtime('United States');
        $countries['UY'] = Gpf_Lang::_runtime('Uruguay');
        $countries['UZ'] = Gpf_Lang::_runtime('Uzbekistan');
        $countries['VA'] = Gpf_Lang::_runtime('Holy See (Vatican City State)');
        $countries['VC'] = Gpf_Lang::_runtime('Saint Vincent and the Grenadines');
        $countries['VE'] = Gpf_Lang::_runtime('Venezuela');
        $countries['VG'] = Gpf_Lang::_runtime('Virgin Islands, British');
        $countries['VI'] = Gpf_Lang::_runtime('Virgin Islands, U.S.');
        $countries['VN'] = Gpf_Lang::_runtime('Vietnam');
        $countries['VU'] = Gpf_Lang::_runtime('Vanuatu');
        $countries['WF'] = Gpf_Lang::_runtime('Wallis and Futuna');
        $countries['WS'] = Gpf_Lang::_runtime('Samoa');
        $countries['YE'] = Gpf_Lang::_runtime('Yemen');
        $countries['YT'] = Gpf_Lang::_runtime('Mayotte');
        $countries['RS'] = Gpf_Lang::_runtime('Serbia');
        $countries['ZA'] = Gpf_Lang::_runtime('South Africa');
        $countries['ZM'] = Gpf_Lang::_runtime('Zambia');
        $countries['ME'] = Gpf_Lang::_runtime('Montenegro');
        $countries['ZW'] = Gpf_Lang::_runtime('Zimbabwe');
        $countries['A1'] = Gpf_Lang::_runtime('Anonymous Proxy');
        $countries['A2'] = Gpf_Lang::_runtime('Satellite Provider');
        $countries['O1'] = Gpf_Lang::_runtime('Other');
        $countries['AX'] = Gpf_Lang::_runtime('Aland Islands');
        $countries['GG'] = Gpf_Lang::_runtime('Guernsey');
        $countries['IM'] = Gpf_Lang::_runtime('Isle of Man');
        $countries['JE'] = Gpf_Lang::_runtime('Jersey');
        return $countries;
    }


    /**
     * @param $countryStatus (E - Enabled, D - Disabled, defined in Gpf_Db_Country)
     */
    public static function insertCountriesToDB($countryStatus) {
        if(self::$countries == null){
            self::$countries = self::createCountries();
        }
        foreach (self::$countries as $code => $name) {
            $country = new Gpf_Db_Country();
            $country->setCountryCode($code);
            $country->setCountry($name);
            $country->setStatus($countryStatus);
            $country->setAccountId(Gpf_Session::getAuthUser()->getAccountId());
            try {
                $country->save();
            } catch (Gpf_DbEngine_Row_ConstraintException $e) {
            }
        }
    }
}

?>
