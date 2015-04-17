<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test\MetaData\Cache;

use Volcanus\Database\MetaData\Table;
use Volcanus\Database\MetaData\Column;

/**
 * Abstract class for CacheProcessorTest
 *
 * @author k.holy74@gmail.com
 */
abstract class AbstractCacheProcessorTest extends \PHPUnit_Framework_TestCase
{

	protected $cacheDir;

	public function setUp()
	{
		$this->cacheDir = __DIR__ . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
	}

	public function tearDown()
	{
		$this->clearDirectory();
	}

	protected function buildMetaTables()
	{
		return array(
			new Table(array(
				'name' => 'users',
				'comment' => 'Table of Users',
				'columns' => $this->buildMetaColumns(),
			)),
		);
	}

	protected function buildMetaColumns()
	{
		return array(
			'id' => new Column(array(
				'name' => 'user_id',
				'type' => 'integer',
				'maxLength' => '11',
				'scale' => null,
				'binary' => false,
				'default' => null,
				'notNull' => true,
				'primaryKey' => true,
				'uniqueKey' => true,
				'autoIncrement' => false,
				'comment' => 'Primary key of User',
			)),
			'name' => new Column(array(
				'name' => 'user_name',
				'type' => 'varchar',
				'maxLength' => '255',
				'scale' => null,
				'binary' => false,
				'default' => null,
				'notNull' => true,
				'primaryKey' => false,
				'uniqueKey' => false,
				'autoIncrement' => false,
				'comment' => 'Name of User',
			)),
		);
	}

	protected function clearDirectory()
	{
		$it = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($this->cacheDir, \FilesystemIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::CHILD_FIRST
		);
		foreach ($it as $file) {
			if ($file->isDir()) {
				if ($file->getRealpath() !== $this->cacheDir) {
					rmdir($file);
				}
			} elseif ($file->isFile() && $file->getBaseName() !== '.gitignore') {
				unlink($file);
			}
		}
	}

}
