<?php
/**
 * Created by Ewebify, LLC.
 * Date: 1/9/18
 * Time: 9:02 AM
 */

namespace PapaLocal\Command;

use PapaLocal\Core\Security\Cryptographer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Class CreateCryptoSalt.
 *
 * @package PapaLocal\Command
 */
class CreateCryptoSalt extends Command
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        // configure command
        $this->setName('security:create-salt')
            ->setDescription('Cryptographically secure SALT generator')
            ->setHelp('This command generates a secure salt.')
            ->addArgument('length', InputArgument::OPTIONAL, 'The length of the salt.');

    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // fetch input
        $length = $input->getArgument('length');

        // fetch crypto class
        $crypto = new Cryptographer();

        // output salt
        $output->writeln($crypto->createSalt($length));
    }
}