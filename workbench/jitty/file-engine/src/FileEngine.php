<?php
namespace Jitty\FileEngine;

class FileEngine {

	protected $rootUrl = false;

	function __construct($rootUrl = false)
	{
		$this->rootUrl = $rootUrl;
	}

	public function setRootPath($rootUrl)
	{
		$this->rootUrl = $rootUrl;
		return $this;
	}

	public function getRootPath()
	{
		return $this->rootUrl;
	}

	public function listSources()
	{
		if( $this->rootUrl === false ||  empty($this->rootUrl) ) return false;

		$sources = glob($this->rootUrl."/*");

		$data = array();
		foreach( $sources as $key => $source )
		{
			$originalSource = $source;
			$source = realpath($source);

			try
			{
				$pathInfo = pathinfo($source);
				$basename = $pathInfo['basename'];
				$dirname  = $pathInfo['dirname'];

				// Empty that mean is shortcut
				if( empty($basename) )
				{
					$pathInfo = pathinfo($originalSource);
					$basename = $pathInfo['basename'];
					$dirname  = $pathInfo['dirname'];
				}
			}
			catch(\Exception $e)
			{
				$basename = $dirname = "None";
			}

			try
			{
				$fileType = filetype($source);
			}
			catch(\Exception $e)
			{
				$fileType = "None";
			}

			try
			{
				$fileSize = $this->formatBytes(filesize($source));
			}
			catch(\Exception $e)
			{
				$fileSize = "None";
			}

			try
			{
				$fileTimeStamp = filemtime($source);

				if( date("Y") == date("Y", $fileTimeStamp) &&  date("M") == date("M", $fileTimeStamp) )
				{
					if( date("d") == date("d", $fileTimeStamp) )
						$fileMTime = "Today, ".date("h:i A", $fileTimeStamp);
					elseif ( date("d", strtotime('last day')) == date("d", $fileTimeStamp) )
						$fileMTime = "Yesterday, ".date("h:i A", $fileTimeStamp);
					else
						$fileMTime = date("M d, Y h:i A", $fileTimeStamp);
				}
				else
				{
					$fileMTime = date("M d, Y h:i A", $fileTimeStamp);
				}

				// $interval = date_diff($datetime1, $datetime2);

				//$fileMTime = date("M d, Y h:i A", filemtime($source));
			}
			catch(\Exception $e)
			{
				$fileMTime = "None";
			}

			$data[$key]['index']     = $key;
			$data[$key]['fullPath']  = $source;
			$data[$key]['baseName']  = $basename;
			$data[$key]['dirName']   = $dirname;
			$data[$key]['type']      = $fileType;
			$data[$key]['fileSize']  = $fileSize;
			$data[$key]['fileMTime'] = $fileMTime;
		}

		return $data;
	}

	public  function formatBytes($size) {
		$mod   = 1024;

		if( $size <= 0 || !is_numeric($size) ) return "None";

		$units = array('B', 'KB', 'MB', "GB", "TB", "PB");

		for ($i = 0; $size >= $mod; $i++)
		{
			$size /= $mod;
		}

		try
		{
			$result = round($size, 2) . ' ' . $units[$i];
		}
		catch(\Exception $e)
		{
			return "None";
		}
		return $result;
	}
}