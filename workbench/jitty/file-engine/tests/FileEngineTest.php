<?php

use Jitty\FileEngine\FileEngine;

class FileEngineTest extends PHPUnit_Framework_TestCase
{
	public $rootPath = "./demo_data";

	public function  __construct()
	{
		$this->rootPath = realpath($this->rootPath);
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
}