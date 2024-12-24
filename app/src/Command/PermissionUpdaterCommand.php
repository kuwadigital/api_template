<?php

namespace App\Command;

use App\Entity\Security\Permission;
use App\Enum\PermissionAction;
use App\Repository\Security\PermissionRepository;
use App\Service\EntityNameService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:permission:update',
    description: 'Command used to update the Permissions in the database.',
)]
class PermissionUpdaterCommand extends Command
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected EntityNameService $entityNameService,
        protected PermissionRepository $permissionRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        // $this
        //     ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
        //     ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        // ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->note("Starting importing new Permission if not created");
        
        // $arg1 = $input->getArgument('arg1');

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        // if ($input->getOption('option1')) {
        //     // ...
        // }
        
        $i = 0;
        $batchSize = 30; // doctrine recommendations

        foreach($this->entityNameService->getAllEntityNames() as $entityName) {
            
            foreach (PermissionAction::cases() as $action) {
                $checkExist = $this->permissionRepository->findBy([
                    'entityName' => $entityName,
                    'permissionAction' => $action
                ]);

                if (!$checkExist) {

                    $permission = new Permission();
                    $permission->setEntityName($entityName);
                    $permission->setPermissionAction($action->value);
                    $permission->setDescription(sprintf("Entity [%s] access permisssion action [%s]", $entityName, $action->value));
                    $this->entityManager->persist($permission);
                    
                }
            }
            
            if ($i % $batchSize == 0) {
                $this->entityManager->flush();
            }
        }

        $this->entityManager->flush();

        $io->success('Command executed successfully !');

        return Command::SUCCESS;
    }
}
