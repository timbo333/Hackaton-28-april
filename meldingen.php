<?php
	/*
	$curl = curl_init();

	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,	true);
	curl_setopt($curl, CURLOPT_VERBOSE,			true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,	true);
	curl_setopt($curl, CURLOPT_HEADER,			false);

	curl_setopt($curl, CURLOPT_URL, 'http://drimble.nl/112/');
	$result = curl_exec($curl);

	$doc = new DomDocument();
	$doc->validateOnParse = true;
	@$doc->LoadHTML($result);

	$table = $doc->getElementsByTagName('table');

	var_dump($table);
	*/


	print(json_encode(array(array(
		'description'	=> 'A1 13163 amsterdam lauernessestraat h',
		'date'			=> '28-04-2015',
		'time'			=> '12:56',
		'adress'		=> 'Lauernessestraat',
		'zip'			=> '1061ES',
		'city'			=> 'Amsterdam',
		'type'			=> 'ambulance'
	))));