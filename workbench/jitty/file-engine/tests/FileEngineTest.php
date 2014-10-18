<?php

use Jitty\FileEngine\FileEngine;

class FileEngineTest extends PHPUnit_Framework_TestCase
{
	public $rootPath;

	public function  setUp()
	{
		$this->rootPath = realpath(dirname(__FILE__)."/demo_data");
	}

	public function testDefineRootPathWithConstruct()
	{
		$fileEngineObj = new FileEngine($this->rootPath);
		$this->assertEquals($fileEngineObj->getRootPath(), $this->rootPath);
	}

	public function testDefineRootPathWithSetRootPathMethod()
	{
		$fileEngineObj = new FileEngine;
		$fileEngineObj->setRootPath($this->rootPath);
		$this->assertEquals($fileEngineObj->getRootPath(), $this->rootPath);
	}

	public function testGetRootPathWhenNotYetDefineRootPath()
	{
		$fileEngineObj = new FileEngine;
		$this->assertFalse($fileEngineObj->getRootPath(), false);
	}

	public function testConvertingMethodFromBytes()
	{
		$fileEngineObj = new FileEngine;
		$this->assertEquals($fileEngineObj->formatBytes(0), "None");
		$this->assertEquals($fileEngineObj->formatBytes(-1), "None");
		$this->assertEquals($fileEngineObj->formatBytes('A'), "None");
		$this->assertEquals($fileEngineObj->formatBytes(1), "1 B");
		$this->assertEquals($fileEngineObj->formatBytes(1023), "1023 B");
		$this->assertEquals($fileEngineObj->formatBytes(1024), "1 KB");
		$this->assertEquals($fileEngineObj->formatBytes(1025), "1 KB");
		$this->assertEquals($fileEngineObj->formatBytes(4929), "4.81 KB");
		$this->assertEquals($fileEngineObj->formatBytes(40000), "39.06 KB");
		$this->assertEquals($fileEngineObj->formatBytes(1048576), "1 MB");
		$this->assertEquals($fileEngineObj->formatBytes(1038090.24), "1013.76 KB");
		$this->assertEquals($fileEngineObj->formatBytes(4000000), "3.81 MB");
		$this->assertEquals($fileEngineObj->formatBytes(1072693248), "1023 MB");
		$this->assertEquals($fileEngineObj->formatBytes(1073741824), "1 GB");
	}

	public function testGetResourceWhenNotDefineOrEmptyRootPath()
	{
		$fileEngineObj = new FileEngine;
		$this->assertFalse($fileEngineObj->listSources(), false);
		$this->assertFalse($fileEngineObj->setRootPath('')->listSources(), false);
	}

	public function testGetResources()
	{
		$fileEngineObj = new FileEngine($this->rootPath);
		$sources = $fileEngineObj->listSources();

		$this->assertCount(5, $sources);
		$this->assertEquals($sources[0]['baseName'], "css");
		$this->assertEquals($sources[0]['type'], "dir");

		$this->assertEquals($sources[1]['baseName'], "demo.txt");
		$this->assertEquals($sources[1]['type'], "file");

		$this->assertEquals($sources[2]['baseName'], "demo2.txt");
		$this->assertEquals($sources[2]['type'], "file");

		$this->assertEquals($sources[3]['baseName'], "fonts");
		$this->assertEquals($sources[3]['type'], "dir");

		$this->assertEquals($sources[4]['baseName'], "js");
		$this->assertEquals($sources[4]['type'], "dir");

	}
}