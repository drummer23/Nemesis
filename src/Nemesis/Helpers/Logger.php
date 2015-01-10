<?php
/**
 * Created by PhpStorm.
 * User: Puncher
 * Date: 10/01/15
 * Time: 15:07
 */

namespace Nemesis\Helpers;

use Symfony\Component\Console\Output\OutputInterface;


class Logger {

    private $output;

    public function __construct (OutputInterface $output)
    {
        $this->output = $output;
    }


    public function logInfo ($message)
    {
        $this->output->writeln('<info>' . $message . '</info>');
    }

    public function logComment ($message)
    {
        $this->output->writeln('<comment>' . $message . '</comment>');
    }

    public function logQuestion ($message)
    {
        $this->output->writeln('<question>' . $message . '</question>');
    }

    public function logError ($message)
    {
        $this->output->writeln('<error>' . $message . '</error>');
    }
}
