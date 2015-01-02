<?php

namespace Nemesis\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProfileCommand extends Command {

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
        $filename = $input->getArgument('filename');
        if (!$filename) {
            $filename = __DIR__ . '/../../../test/example.curl';
        }

        $handle = fopen($filename, "r");
        $contents = fread($handle, filesize($filename));
        fclose($handle);


        $output->writeln('end');
    }
}