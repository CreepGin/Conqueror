<?php

use \App\Node;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
	$projects = Node::distinct("project")->get()->pluck("project")->toArray();
    return view('welcome', ["projects" => array_unique($projects)]);
});

Route::resource("node", "NodeController");

get("/delete/{project}", function($project){
	Node::whereProject($project)->delete();
	return "success";
});

Route::get('/{project}', function ($project) {
	$nodes = Node::whereProject($project)->get();
	if ($nodes->count() == 0) {
		Node::create([
			"project" => $project,
			"name" => $project
		]);
		$nodes = Node::whereProject($project)->get();
	}
    return view('project', ["project" => $project, "nodes" => $nodes]);
});