<?php

namespace Nemesis\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Nemesis\Helpers\Logger;

class ProfileCommand extends Command {

    private $pattern = Array(

        "Url" => "/(?<=curl\s)'[^']*'/",
        "Header" => "/(?<=-H\s)'[^']*'/",
        "Data" => "/(?<=--data\s')[^=]*=[^\&]*/"
    );

    protected function configure()
    {
        $this
            ->setName('nemesis:profile')
            ->setDescription('use a curl to generate a profile')
            //TODO: set required
            ->addArgument(
                'filename',
                InputArgument::OPTIONAL,
                'The Curl Input File?'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = new Logger($output);


        $inpFileName = $input->getArgument('filename');
        if (!$inpFileName) {
            $inpFileName = __DIR__ . '/../../../test/example.curl';
        }

        $handle = fopen($inpFileName, "r");
        $contents = fread($handle, filesize($inpFileName));
        fclose($handle);

        $profile = explode('/','/'.$inpFileName);
        rsort($profile);
        $profile = $profile[0];

        $prepJson = Array();

        foreach($this->pattern as $key => $curmuster){

            $success = preg_match_all($curmuster, $contents, $matches);

            foreach ($matches[0] as $match) {
                $prepJson[$key][] = ($match);
                echo "$key: " . $match . PHP_EOL;
            }
        }

        $json = json_encode($prepJson);

        $outFileName = __DIR__ . '/../../../profiles/' . $profile . '.json';

        $handle = fopen($outFileName,'w');
        $success = fwrite  ($handle, $json);

        if(!$success)
        {
            //TODO: Report Error
        }

        fclose($handle);

        //TODO: Report Success
        $logger->logInfo('end');
    }
}