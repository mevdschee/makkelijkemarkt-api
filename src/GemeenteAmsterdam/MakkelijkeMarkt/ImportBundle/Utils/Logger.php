<?php
/*
 *  Copyright (C) 2017 X Gemeente
 *                     X Amsterdam
 *                     X Onderzoek, Informatie en Statistiek
 *
 *  This Source Code Form is subject to the terms of the Mozilla Public
 *  License, v. 2.0. If a copy of the MPL was not distributed with this
 *  file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

namespace GemeenteAmsterdam\MakkelijkeMarkt\ImportBundle\Utils;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Logger implements LoggerInterface
{
    /**
     * @var array
     */
    protected $outputStreams = [];

    /**
     * @var array
     */
    protected $store = [];

    /**
     * @param mixed $output "stdout"|"STDOUT"|"store"|LoggerInterface|OutputInterface
     */
    public function addOutput($output)
    {
        $this->outputStreams[] = $output;
    }

    /**
     * (non-PHPdoc)
     * @see \Psr\Log\LoggerInterface::emergency()
     */
    public function emergency($message, array $context = array())
    {
        return $this->log('emergency', $message, $context);
    }

    /**
     * (non-PHPdoc)
     * @see \Psr\Log\LoggerInterface::alert()
     */
    public function alert($message, array $context = array())
    {
        return $this->log('alert', $message, $context);
    }

    /**
     * (non-PHPdoc)
     * @see \Psr\Log\LoggerInterface::critical()
     */
    public function critical($message, array $context = array())
    {
        return $this->log('critical', $message, $context);
    }

    /**
     * (non-PHPdoc)
     * @see \Psr\Log\LoggerInterface::error()
     */
    public function error($message, array $context = array())
    {
        return $this->log('error', $message, $context);
    }

    /**
     * (non-PHPdoc)
     * @see \Psr\Log\LoggerInterface::warning()
     */
    public function warning($message, array $context = array())
    {
        return $this->log('warning', $message, $context);
    }

    /**
     * (non-PHPdoc)
     * @see \Psr\Log\LoggerInterface::notice()
     */
    public function notice($message, array $context = array())
    {
        return $this->log('notice', $message, $context);
    }

    /**
     * (non-PHPdoc)
     * @see \Psr\Log\LoggerInterface::info()
     */
    public function info($message, array $context = array())
    {
        return $this->log('info', $message, $context);
    }

    /**
     * (non-PHPdoc)
     * @see \Psr\Log\LoggerInterface::debug()
     */
    public function debug($message, array $context = array())
    {
        return $this->log('debug', $message, $context);
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        foreach ($this->outputStreams as $outputStream)
        {
            switch (true)
            {
                case ($outputStream === 'stdout'):
                case ($outputStream === 'STDOUT'):
                    echo '[' . $level . '] ' . $message . ' : ' . json_encode($context) . PHP_EOL;
                    break;
                case ($outputStream === 'store'):
                    $this->store[] = ['level' => $level, 'message' => $message, 'context' => json_encode($context)];
                    break;
                case ($outputStream instanceof LoggerInterface):
                    $outputStream->log($level, $message, $context);
                    break;
                case ($outputStream instanceof OutputInterface):
                    switch ($level)
                    {
                        case 'emergency':
                        case 'alert':
                        case 'critical':
                        case 'error':
                            $outputStream->writeln('<error>' . $level . '</error>');
                        case 'warning':
                        case 'notice':
                            $outputStream->writeln('<notice>' . $level . '</notice>');
                        case 'info':
                        default:
                            $outputStream->writeln('<info>' . $level . '</info>');
                    }
                    $outputStream->writeln($message);
                    if ($context !== [])
                        $outputStream->writeln(json_encode($context));
                    break;
            }
        }
    }
}