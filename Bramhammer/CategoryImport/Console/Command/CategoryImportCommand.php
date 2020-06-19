<?php
	/**
	 * Copyright © 2020 Bram Hammer. All rights reserved.
	 */
	
	namespace Bramhammer\CategoryImport\Console\Command;
	
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Magento\Framework\ObjectManagerInterface;
	
	/**
	 * Class CategoryImportCommand
	 */
	class CategoryImportCommand extends Command
	{
		
		private $objectmanager;
		
		private $output;
		
		private $exitOnError = false;
		
		const EXIT = "exit";
		
		/**
		 * @param ObjectManagerInterface $objectmanager
		 */
		public function __construct(ObjectManagerInterface $objectmanager)
		{
			$this->objectmanager = $objectmanager;
			
			parent::__construct();
		}
		
		/**
		 * {@inheritdoc}
		 */
		protected function configure()
		{
			$this->setName('bramhammer:importcategories')
				->setDescription('Import all categories')
				->setDefinition([
					new InputOption(
						self::EXIT,
						'e',
						InputOption::VALUE_NONE,
						'when used, script will stop on first error.'
					)
				]);
			
			parent::configure();
		}
		
		/**
		 * {@inheritdoc}
		 */
		protected function execute(InputInterface $input, OutputInterface $output)
		{
			$this->exitOnError = $input->getOption(self::EXIT);
			
			$this->output = $output;
			$parentid = 2; // Change this to YOUR parent category id! (default category)
			// change the category structure here. example is given
			$categorys = [
				'main category' => [
					"sub category",
					"sub category 2",
				],
				"main category 2" => [
					"sub category 3" => [
						"sub sub category",
						"sub sub category 2",
					],
					"sub category 4",
				],
				"main category 3",
			];
			
			foreach($categorys as $parent => $category) {
				$this->doLoop($parentid,$parent,$category);
			}
		}
		
		/**
		 * start loop
		 * @param string $parentId
		 * @param string $parent
		 * @param string|array $category
		 */
		protected function doLoop($parentId,$parent,$category){
			if(is_array($category)){
				$newparent = $this->createCategory($parent,$parentId);
				foreach($category as $parent => $cat) {
					$this->doLoop($newparent,$parent,$cat);
				}
			}else{
				$this->createCategory($category,$parentId);
			}
		}
		
		/**
		 * Adding country
		 * @param string $categoryName
		 * @param string $parentId
		 */
		protected function createCategory($categoryName, $parentId){
			
			$parentCategory = $this->objectmanager
				->create('Magento\Catalog\Model\Category')
				->load($parentId);
			if($parentCategory) {
				$category = $this->objectmanager->create('Magento\Catalog\Model\Category');
				try {
					$category->setPath($parentCategory->getPath())->setParentId($parentId)->setName($categoryName)
						//->setIncludeInMenu(1) // remove comment to add category to menu
						->setIsActive(true);
					$category->save();
				}catch(\Exception $e){
					$this->output->writeln('<error>Can\'t save category '.$categoryName.'.</error>');
					$this->output->writeln('<error>'.$e->getMessage().'.</error>');
					if($this->exitOnError)
						die();
				}
				return $category->getId();
			}else{
				$this->output->writeln('<error>Can\'t find parent category of '.$categoryName.'.</error>');
				if($this->exitOnError)
					die();
			}
		}
	}
