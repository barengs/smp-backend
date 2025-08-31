<?php

require_once 'vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;

// Create a service container
$container = new Container();
$container->instance('events', new Dispatcher());

// Create a Capsule instance
$capsule = new Capsule($container);
$capsule->addConnection([
    'driver'    => 'pgsql',
    'host'      => '127.0.0.1',
    'port'      => '5432',
    'database'  => 'smp_backend',
    'username'  => 'postgres',
    'password'  => 'rahasia',
    'charset'   => 'utf8',
    'prefix'    => '',
    'schema'    => 'public',
]);

// Make this Capsule instance available globally via static methods
$capsule->setAsGlobal();

// Setup the Eloquent ORM
$capsule->bootEloquent();

// Now let's test our queries
echo "=== Testing Staff-Role Queries ===\n";

// Test 1: Get all users with staff
echo "\n1. All users with staff:\n";
$usersWithStaff = Capsule::table('users')
    ->join('staff', 'users.id', '=', 'staff.user_id')
    ->select('users.id', 'users.name', 'users.email', 'staff.id as staff_id', 'staff.first_name', 'staff.last_name')
    ->get();

foreach ($usersWithStaff as $user) {
    echo "  User: {$user->name} (ID: {$user->id}) - Staff: {$user->first_name} {$user->last_name} (ID: {$user->staff_id})\n";
}

// Test 2: Get all users with target roles
echo "\n2. All users with 'asatidz' or 'walikelas' roles:\n";
$usersWithRoles = Capsule::table('users')
    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
    ->whereIn('roles.name', ['asatidz', 'walikelas'])
    ->select('users.id', 'users.name', 'users.email', 'roles.name as role_name')
    ->get();

foreach ($usersWithRoles as $user) {
    echo "  User: {$user->name} (ID: {$user->id}) - Role: {$user->role_name}\n";
}

// Test 3: Get users with both staff and target roles
echo "\n3. Users with both staff and 'asatidz' or 'walikelas' roles:\n";
$usersWithBoth = Capsule::table('users')
    ->join('staff', 'users.id', '=', 'staff.user_id')
    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
    ->whereIn('roles.name', ['asatidz', 'walikelas'])
    ->select('users.id', 'users.name', 'users.email', 'roles.name as role_name', 'staff.first_name', 'staff.last_name')
    ->get();

if ($usersWithBoth->isEmpty()) {
    echo "  No users found with both staff and target roles.\n";
} else {
    foreach ($usersWithBoth as $user) {
        echo "  User: {$user->name} (ID: {$user->id}) - Role: {$user->role_name} - Staff: {$user->first_name} {$user->last_name}\n";
    }
}
