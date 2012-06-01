<?php
	//Set the language array
	$lang = array(
		'en'		=> 'English', 
		'enutf8' 	=> 'English (UTF-8)',
		'bg'		=> 'Bulgarian',
		'cs'		=> 'Czech',
		'da'		=> 'Dansk',
		'de'		=> 'Deutsch',
		'es'		=> 'Espa�ol',
		'fr'		=> 'Fran�ais',
		'it'		=> 'Italiano',
		'ja'		=> 'Japanese',
		'nl'		=> 'Nederlands',
		'pl'		=> 'Polski',
		'ptbr'		=> 'Portugu�s - Brasil',
		'ro'		=> 'Rom�n�',
		'ru'		=> 'Russian'
	);

	//Create the select box and options
	echo "<SELECT name=\"lang\">\n";
		foreach($lang as $key => $value) {
			//Set the default language automatically based on global webgui language
			$selected = ($key == $GLOBALS["language"]) ? " selected='selected'" : '';
			//Now create the <options> list
			echo "<option value='$key'$selected>$value</option>\n";
	}
	echo "</SELECT></TD></TR>\n";