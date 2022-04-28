<?php

namespace Deployer;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

require __ROOT__.'/vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(__ROOT__);
if (is_file(__ROOT__.'/.env')) $dotenv->load();

// Project
set('application', getenv('APP_NAME'));
set('allow_anonymous_stats', false);
set('keep_releases', 1);

// Default
set('default_stage', 'local');

// Repository
set('repository', getenv('DP_REPOSITORY'));
set('branch', getenv('DP_BRANCH'));
set('git_recursive', false); // Ignore submodules
set('git_tty', true); // Known host & passphrase
set('import_path', __ROOT__.'/vendor/abetter/wordpress/database/');

//argument('import', InputArgument::OPTIONAL, 'Import file.');
option('import', null, InputOption::VALUE_OPTIONAL, 'Import file.');

// Setup
set('shared_files', [
    '.env'
]);
set('shared_dirs', [
    'storage'
]);
add('writable_dirs', [
	'bootstrap/cache',
    'storage'
]);

// Hosts
localhost('local')->set('stage','local')->set('hostname','127.0.0.1')->set('server','127.0.0.1')->set('deploy_path',env('DP_LOCAL_PATH'))->set('deploy_unlock',TRUE);
if ($s = env('DP_DEV_SERVER')) {
	host('dev')->set('stage','dev')->set('hostname',$s)->set('server',$s)->set('deploy_path',env('DP_DEV_PATH'))->set('deploy_unlock',env('DP_DEV_UNLOCK'))->set('branch',$b=env('DP_DEV_BRANCH')?:get('branch'));
}
if ($s = env('DP_DEMO_SERVER')) {
	host('demo')->set('stage','demo')->set('hostname',$s)->set('server',$s)->set('deploy_path',env('DP_DEMO_PATH'))->set('deploy_unlock',env('DP_DEMO_UNLOCK'))->set('branch',$b=env('DP_DEMO_BRANCH')?:get('branch'));
}
if ($s = env('DP_STAGE_SERVER')) {
	host('stage')->set('stage','stage')->set('hostname',$s)->set('server',$s)->set('deploy_path',env('DP_STAGE_PATH'))->set('deploy_unlock',env('DP_STAGE_UNLOCK'))->set('branch',$b=env('DP_STAGE_BRANCH')?:get('branch'));
}
if ($s = env('DP_PRODUCTION_SERVER')) {
	host('production')->set('stage','production')->set('hostname',$s)->set('server',$s)->set('deploy_path',env('DP_PRODUCTION_PATH'))->set('deploy_unlock',env('DP_PRODUCTION_UNLOCK'))->set('branch',$b=env('DP_PRODUCTION_BRANCH')?:get('branch'));
}

// -------------------------------------

function writeLine($message="",$output="",$style="info") {
	writeln(date("\[H:i:s\] ")."<$style>".$message.(($output)?": ".$output:"")."</$style>");
}

function writeRun($command,$message="") {
	writeLine(($message)?$message:$command,run($command));
}

function writeRunLocally($command,$message="") {
	writeLine(($message)?$message:$command,runLocally($command));
}

// ---

task('hello', function () {
	$stage = get('stage');
	$lock = ($unlock = get('deploy_unlock')) ? FALSE : TRUE;
	writeLine('{{ server }}');
	cd('{{ deploy_path }}');
	writeRun("hostname");
	writeRun("whoami","username");
	writeRun("pwd","destination");
    writeLine("Hello world, ready to go @ ".ucwords($stage));
	writeLine(ucwords($stage).' is '.(($lock)?'LOCKED':'OPEN').' for push! (set DP_'.strtoupper($stage).'_UNLOCK in .ENV)');
});
