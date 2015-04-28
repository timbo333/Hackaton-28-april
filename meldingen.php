<?php
	
	function startsWith($haystack, $needle) {
	    // search backwards starting from haystack length characters from the end
	    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
	}

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

	$table = $doc->getElementsByTagName('table')->item(0);

	$links = $table->getElementsByTagName('a');
	
	$maanden = array('januari','februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december');
	
	$response = array();

	foreach($links as $link) {
		$uri = $link->getAttribute('href');

		curl_setopt($curl, CURLOPT_URL, 'http://drimble.nl/' . $uri);
		$result = curl_exec($curl);

		$doc2 = new DomDocument();
		$doc2->validateOnParse = true;
		@$doc2->LoadHTML($result);

		$tables = $doc2->getElementsByTagName('table');

		$tds_1 = $tables->item(0)->getElementsByTagName('td');

		$datetime = $tds_1->item(3)->textContent;
		$datetime = explode(' om ', $datetime);
		foreach($maanden as $i => $maand) {
			$datetime[0] = str_replace(' ' . $maand . ' ', '-' . ($i+1) . '-', $datetime[0]);
		}

		$tds_2 = $tables->item(1)->getElementsByTagName('td');

		$h1_text = $doc2->getElementsByTagName('h1')->item(0)->textContent;
		$type = null;
		if(startsWith($h1_text, 'Brandweer')) {
			$type = 'brandweer';
		} else
		if(startsWith($h1_text, 'Ambulance')) {
			$type = 'ambulance';
		} else
		if(startsWith($h1_text, 'Politie')) {
			$type = 'politie';
		} 

		$data = array(
			'description'	=> trim($tds_1->item(1)->textContent, "'"),
			'date'			=> $datetime[0],
			'time'			=> $datetime[1],
			//'address'		=> $tds_2->item(1)->textContent,
			//'zip'			=> $tds_2->item(9)->textContent,
			//'city'			=> $tds_2->item(3)->textContent,
			'type'			=> $type
		);

		foreach($tds_2 as $i => $td) {
			switch($td->textContent) {
				case 'Plaats:':
				{
					$data['city'] = $tds_2->item($i+1)->textContent;
				}
				break;
				case 'Postcode:':
				{
					$data['zip'] = $tds_2->item($i+1)->textContent;
				}
				break;
				case 'Adres:':
				{
					$data['adress'] = $tds_2->item($i+1)->textContent;
				}
				break;
			}
		}

		$response[] = $data;

		//var_dump($data);
		//exit();
	}
	
	print(json_encode($response));

	/*
	print(json_encode(array(array(
		'description'	=> 'A1 13163 amsterdam lauernessestraat h',
		'date'			=> '28-04-2015',
		'time'			=> '12:56',
		'adress'		=> 'Lauernessestraat',
		'zip'			=> '1061ES',
		'city'			=> 'Amsterdam',
		'type'			=> 'ambulance'
	))));
	*/
