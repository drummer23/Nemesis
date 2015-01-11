<?php

namespace Nemesis\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
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

        $logger->info('Reading ' . $inpFileName);
        $handle = fopen($inpFileName, "r");
        $contents = fread($handle, filesize($inpFileName));
        fclose($handle);

        $profile = explode('/','/'.$inpFileName);
        rsort($profile);
        $profile = $profile[0];

        $logger->info('Generating profile "' . $profile . '"');

        $json = Array();

        foreach($this->pattern as $key => $curmuster){

            $success = preg_match_all($curmuster, $contents, $matches);

            foreach ($matches[0] as $match) {
                $json[$key][] = ($match);
                $logger->debug("$key: " . $match);
            }
        }

        $json = json_encode($json);

        $outFileName = __DIR__ . '/../../../profiles/' . $profile . '.json';

        if (file_exists($outFileName))
        {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('File ' . $outFileName . 'already exists. Overwrite (Y/n)? ', true);

            if (!$helper->ask($input, $output, $question)) {
                $logger->info('Aborted');
                return;
            }
        }



        $logger->info('Saving profile to ' . $outFileName);

        $handle = fopen($outFileName,'w');
        $success = fwrite  ($handle, $json);

        if(!$success)
        {
            //TODO: Report Error
        }

        fclose($handle);

        //TODO: Report Success
        $logger->info('Done');
    }
}