<?php
/**
 * Created by PhpStorm.
 * User: joe
 * Date: 8/23/18
 * Time: 8:54 PM
 */


namespace PapaLocal\Command;


use PapaLocal\Core\Factory\GuidFactory;
use PapaLocal\Core\ValueObject\GuidGeneratorInterface;
use PapaLocal\Core\Security\Cryptographer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Class CreateGuid
 *
 * @package PapaLocal\Command
 */
class CreateGuid extends Command
{
    /**
     * @var GuidGeneratorInterface
     */
    private $guidGenerator;

    /**
     * CreateGuid constructor.
     *
     * @param GuidGeneratorInterface $guidGenerator
     * @param null                   $name
     */
    public function __construct(GuidGeneratorInterface $guidGenerator, $name = null)
    {
        parent::__construct($name);

        $this->guidGenerator = $guidGenerator;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        // configure command
        $this->setName('security:create-guid')
             ->setDescription('Create a GUID string')
             ->setHelp('This command generates 36 character unique ID string.');

    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // output salt
        try {
            $output->writeln($this->guidGenerator->generate()->value());
        } catch (\Exception $exception) {
            $output->writeln(sprintf('An exception occurred: %s', $exception->getMessage()));
        }
    }
}