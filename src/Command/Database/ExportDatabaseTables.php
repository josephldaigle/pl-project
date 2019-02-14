<?php
/**
 * Created by Ewebify, LLC.
 * Date: 3/10/18
 * Time: 8:35 AM
 */

namespace PapaLocal\Command\Database;


use PapaLocal\Data\Command\Admin\FetchTableData;
use PapaLocal\Data\Command\Admin\FetchTableNames;
use PapaLocal\Data\Command\Factory\CommandFactory;
use PapaLocal\Data\DataService;
use PapaLocal\Core\Data\SchemaRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * ExportDatabaseTables.
 *
 * @package PapaLocal\Command\Database
 */
class ExportDatabaseTables extends Command
{
    /**
     * @var DataService
     */
    private $persistence;

    /**
     * @var CommandFactory
     */
    private $commandFactory;

    /**
     * @var SerializerInterface
     */
    private $serializer;

	/**
	 * @var SchemaRepository
	 */
    private $schemaRepository;


    /**
     * @inheritDoc
     */
    public function __construct(DataService $dataService,
                                CommandFactory $commandFactory,
                                SerializerInterface $serializer,
                                SchemaRepository $schemaRepository)
    {
        $this->persistence = $dataService;
        $this->commandFactory = $commandFactory;
        $this->serializer = $serializer;
        $this->schemaRepository = $schemaRepository;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('database:export-tables')
            ->setDescription('Export all tables to csv files, named after the tables.')
            ->setDefinition(array(
                new InputArgument('table', InputArgument::OPTIONAL, 'A table name to export')))
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> is a utility for exporting database data:

  <info>php %command.full_name%</info>

To export all tables, do not provide the table name argument when executing the command.

  <info>bin/console %command.full_name% --table Address</info>
EOF
        );

    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // fetch the io style
        $io = new SymfonyStyle($input, $output);

        // confirm user wants to proceed with dangerous operation
        $confirmed = $io->confirm('WARNING! This operation will overwrite the files in tests/_data/ directory of this project. Are you sure'
                .' you want to proceed?', false);

        // if user does not confirm, cancel command
        if (!$confirmed) {
            $output->writeln('Operation cancelled.');
            return;
        }

        // determine if use wants to create empty files for empty tables
        $createEmptyFiles = $io->choice(sprintf('Would you like to create csv files for empty tables?'), array('yes', 'no'), 'yes');

        if ($tableName = $input->getArgument('table')) {
            // user selected only one table name
            $this->createCsvFile($tableName, $output, $createEmptyFiles);

        } else {
            // user has not specified a table name, export all
            $tableNames = $this->persistence->execute($this->commandFactory->createCommand(FetchTableNames::class));

            foreach($tableNames as $tableName) {
                $this->createCsvFile($tableName['table_name'], $output, $createEmptyFiles);
            }
        }
    }


    private function createCsvFile(string $tableName, OutputInterface $output, string $createEmptyFiles)
    {

        $data = $this->persistence->execute($this->commandFactory->createCommand(
            FetchTableData::class, array($tableName)));

        $fileName = 'tests/_data/' . $tableName . '.csv';

        if ((count($data) < 1) && 'yes' === $createEmptyFiles) {
	        // table contains no data, export only columns
            $output->writeln('creating csv for ' . $tableName);
            $output->writeln(sprintf('no data present in %s, exporting column names only', $tableName));

            $columnNames = $this->schemaRepository->fetchColumnNames($tableName);


            fopen($fileName, 'w+');
            file_put_contents(
                $fileName,
                implode(",", $columnNames)
            );



        } elseif (count($data) > 1) {
        	//
            $output->writeln('creating csv for ' . $tableName);
            fopen($fileName, 'w');
            file_put_contents(
                $fileName,
                $this->serializer->serialize($data, 'csv')
            );
        }

        return;

    }

}