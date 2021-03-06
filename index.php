<?php
require 'bootstrap.php';

use Jitty\FileEngine\FileEngine;

$app = new \Slim\Slim(array(
		'log.enable' => true,
		'debug'      => true,
		'templates.path' => './views'
));
$app->hook('slim.before', function () use ($app) {
		$posIndex = strpos( $_SERVER['PHP_SELF'], '/index.php');
		$baseUrl  = substr( $_SERVER['PHP_SELF'], 0, $posIndex);
		$app->view()->appendData(array('baseUrl' => $baseUrl ));
});

$app->get('/', function () use ($app) {

		$fileEngineObj = new FileEngine();

		$data = array(
			'sources' => $fileEngineObj->setRootPath(BROWSE_URL)->listSources()
		);
		return $app->render('file_panel.php', $data);
});

$app->get('/get-sources', function () use ($app) {
		$path = htmlspecialchars($app->request->params('path'));
		$path = realpath($path);

		$fileEngineObj = new FileEngine(BROWSE_URL);

		$response = $app->response();

		if( $fileEngineObj->canAccessPath($path) )
		{
			$response['Content-Type'] = 'application/json';
			$response['X-Powered-By'] = 'Jitty';
			$response->status(200);

			$sources = $fileEngineObj->setRootPath($path)->listSources();

			if ( $sources === false )
			{
				$sources = array( 'error' => true );
			}
		}
		else
		{
			$sources = array( 'error' => true );
		}

		$response->body(json_encode($sources));
});

$app->get('/loadfile', function() use ($app){
		$path = htmlspecialchars($app->request->params('path'));
		$path = realpath($path);

		$fileEngineObj = new FileEngine(BROWSE_URL);

		$res  = $app->response();

		if( $fileEngineObj->canAccessPath($path) )
		{
			try
			{
				$res['Content-Description']       = 'File Transfer';
				$res['Content-Type']              = 'application/octet-stream';
				$res['Content-Disposition']       = 'attachment; filename=' . basename($path);
				$res['Content-Transfer-Encoding'] = 'binary';
				$res['Expires']                   = '0';
				$res['Cache-Control']             = 'must-revalidate';
				$res['Pragma']                    = 'public';
				$res['Content-Length']            = filesize($path);
				readfile($path);
			}
			catch ( Exception $e )
			{
				echo '<h1>You don\'t have permission to access this file.</h1>';
			}
		}
		else
		{
			echo '<h1>You don\'t have permission to access this file.</h1>';
		}
});


//Start Application
$app->run();