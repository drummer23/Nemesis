<?php

namespace Nemesis\Helpers;

use Symfony\Component\Console\Logger\ConsoleLogger;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\OutputInterface;


class Logger extends ConsoleLogger {

    public function __construct(OutputInterface $output)
    {

        $verbosityLevelMap = array(
            LogLevel::NOTICE => OutputInterface::VERBOSITY_NORMAL,
            LogLevel::INFO => OutputInterface::VERBOSITY_NORMAL,
            LogLevel::DEBUG => OutputInterface::VERBOSITY_NORMAL,
        );

        /*
        $formatLevelMap = array(
        LogLevel::NOTICE => LogLevel::ERROR,
        LogLevel::INFO => LogLevel::ERROR,
        );
        */

        parent::__construct($output,$verbosityLevelMap);
    }
}
