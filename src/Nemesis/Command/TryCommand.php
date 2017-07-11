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
			  )
		;
  	}

  	protected function execute(InputInterface $input, OutputInterface $output)
  	{
		$logger = new Logger($output);
		$dom = new \DOMDocument();

        $url = $input->getArgument('url');



		// Get cURL resource
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
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


		$logger->info('Send Request to ' . $url);



		// Send the request & save response to $resp
		$resp = curl_exec($curl);


		// Close request to clear up some resources
		curl_close($curl);

		$logger->info("Analyze Response from Server");


		$dom->loadHTML($resp);
		$xpath = new \DOMXPath($dom);

		// example 2: for node data in a selected id
		$elements = $xpath->query("/html/body/div/div[@id='login_error']");


		foreach ($elements as $element) {
			$logger->info("Login failed");
			var_dump(trim($element->nodeValue));
		}




		$logger->info('Done');


	}
}

?>
