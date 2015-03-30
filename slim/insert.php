<?php
require "Slim/Slim.php";
 
// create new Slim instance
$app = new Slim();
 
// add new Route
$app->get("/", function () {
    echo "<h1>Hello Slim World</h1>";
});
 
// run the Slim app
$app->run();