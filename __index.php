<?php

require __DIR__ . '/vendor/autoload.php';

use Sienekib\Layers\Connection\Connection;
use Sienekib\Layers\Utils\Config;
use Sienekib\Layers\Facade\DB;
use Sienekib\Layers\Models\BaseModel;
use Spatie\Ignition\Ignition;

new Config([
	// 'driver' => 'mysql',
	'host' => 'localhost',
	'database' => 'cardapio',
	'username' => 'root',
	'password' => '',
	// 'charset' => 'utf8mb4',
	// 'persistent' => true
]);
// new Connection();

// if (file_exists(__DIR__ . '/database/migrations/migration.php')) {
	
// 	require __DIR__ . '/database/migrations/migration.php';
// 	$m = new CreateUsersTable();

// 	$m->down();
// 	$m->up();
// }


//Ignition::make()->renderException(exception);


//DB::table('usersis');


//$data = DB::table('planos')->select('plano_id = ?', [0]);
// $data = DB::raw('select * from planos where plano_id = ?', [4]);

// // var_dump($data);
class User extends BaseModel {
	protected static $table = 'contas';
}

 // $data = User::update([
 // 	'provincia' => 'Kelyson'
 // ], 1);

$data = User::all();
var_dump($data);


// require __DIR__ . '/database/seeders/DatabaseSeeders.php';

// $d = new DatabaseSeeders();
// $d->execute();