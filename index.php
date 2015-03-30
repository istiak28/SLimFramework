<?php

// Resources
//http://www.sitepoint.com/writing-a-restful-web-service-with-slim/
//http://docs.slimframework.com/
//http://www.notorm.com/

error_reporting(0);

require 'vendor/autoload.php';
require 'NOTORM/NotORM.php';
require 'IstiakHelper/UtilityHelper.php';
require 'IstiakHelper/Security.php';

$pdo = new PDO('mysql:host=localhost;dbname=quize;', 'root', '');
$db = new NotORM($pdo);
$app = new \Slim\Slim();
$util = new UtilityHelper();
$security = new Security();

$app->response()->header('Content-Type', 'application/json');


// Users //
// Example url : http://localhost/slim/users

$app->get('/users', function() use ($app, $db, $util) {
    $users = array();
    foreach ($db->users() as $user) {
        $users[] = array(
            'id' => $user['id'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'fathers_name' => $user['fathers_name'],
            'mothers_name' => $user['mothers_name'],
            'email' => $user['email'],
            'mobile_number' => $user['mobile_number'],
            'created' => $user['created'],
            'modified' => $user['modified'],
            'status' => $user['status'],
        );
    }

    $status = ($users) ? true : false;
    $message = "All Users";
    $data = $users;
    echo $util->json_message($status, $message, $data);
});

// Add //
$app->post('/user/add', function() use($app, $db, $util, $security) {
    $user = $app->request()->post();

    $users = array(
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'fathers_name' => $user['fathers_name'],
        'mothers_name' => $user['mothers_name'],
        'email' => $user['email'],
        'password' => $security->cryptPass($user['password']),
        'mobile_number' => $user['mobile_number'],
//        'created' => $user['created'],
//        'modified' => $user['modified'],
//        'status' => $user['status'],
    );
    $result = null;
    $status = false;
    $message = '';
    if (!empty(strlen($users['first_name'])<2)) {
    $message = "First name must be 2 charecter";
    } elseif (empty($users['last_name'])) {
        $message = "Last Name Required";
    } elseif (empty($users['fathers_name'])) {
        $message = "Father Name Required";
    } elseif (empty($users['mothers_name'])) {
        $message = "Mother Name Required";
    } elseif (!filter_var($users['email'], FILTER_VALIDATE_EMAIL)) {
        $message = "Insert a valid email address";
    } elseif (strlen(trim($user['password']))< 5) {
    $message = "Password must be 5 charecters";
    } elseif (empty($users['mobile_number'])) {
        $message = "Mobile Number Required";
    }
    else {
        $result = $db->users()->insert($user);
        $status = true;
        $message = "UserAdded";
        $data = $result;
    }

    echo $util->json_message($status, $message, $data);
});


// Login //
$app->post('/user/login', function () use($app, $db, $util) {
    $user = $app->request()->post();

    $users = $db->users()->where(array(
        'email' => $user['email'],
        'password' => $user['password']
    ));
    // todo update usr cookie
    $users->update(array('cookie' => '$id'));

    $profile = $users->fetch();
    $status = ($profile) ? true : false;
    $message = "User Profile";
    $data = $profile;
    echo $util->json_message($status, $message, $data);
});

// User Id //
$app->get("/user/:id", function ($id) use ($app, $db, $util) {
    $users = $db->users()->where('id', $id);
    if ($users->fetch()) {
        $status = true;
        $message = "User";
        $data = $users["$id"];
        echo $util->json_message($status, $message, $data);
    } else {
        $status = ($users) ? true : false;
        $message = "User ID $id does not exist";
        $data = '';
        echo $util->json_message($status, $message, $data);
    }
});


// Delete // 
// Example url: http://localhost/slim/user/[user_id]

$app->delete('/user/:id', function($id) use($app, $db, $util) {
    $users = $db->users()->where('id', $id);
    if ($users->fetch()) {
        $result = $users->delete();
        $status = true;
        $message = "User deleted successfully";
        $data = $users["$id"];
        echo $util->json_message($status, $message, $data);
    } else {
        echo json_encode(
                array(
                    'status' => false,
                    'message' => 'User id ' . $id . ' does not exist'
                )
        );
    }
    echo json_encode(array("id" => $result["id"]));
});


// Update //
// Example url : http://localhost/slim/user/update/[user_id]
// _METHOD = PUT

$app->put("/user/update/:id", function ($id) use ($app, $db, $util) {
    $users = $db->users()->where("id", $id);
//    $util->pr($users->fetch());
    if ($users->fetch()) {
        $post = $app->request()->put();
        unset($post['_METHOD']);
//        $util->pr($post);
        $result = $users->update($post);
        if ($result) :
            $status = true;
            $message = "User information updated successfully";
            $users_updated = $db->users()->where("id", $id);
            $data = $users_updated->fetch();
            echo $util->json_message($status, $message, $data);
        else:
            $status = false;
            $message = "User information not updated";
            $data = '';
            echo $util->json_message($status, $message, $data);
        endif;
    }

    else {
        $status = ($users) ? true : false;
        $message = "User id $id does not exist";
        $data = '';
        echo $util->json_message($status, $message, $data);
    }
});


// Update Password //
// Example url : http://localhost/slim/user/update/pass/[user_id]
// _METHOD = PUT
$app->put("/user/update/pass/:id", function ($id) use ($app, $db, $util) {
    $users = $db->users()->where("id", $id);
//    $util->pr($users->fetch());
    if ($users->fetch()) {
        $post = $app->request()->put();
        unset($post['_METHOD']);
//        $util->pr($post);
        $result = $users->update($post);
        if ($result) :
            $status = true;
            $message = "User password change successfully";
            $users_updated = $db->users()->where("id", $id);
            $data = $users_updated->fetch();
            echo $util->json_message($status, $message, $data);
        else:
            $status = false;
            $message = "User password can not change successfully";
            $data = '';
            echo $util->json_message($status, $message, $data);
        endif;
    }

    else {
        $status = ($users) ? true : false;
        $message = "User id $id does not exist";
        $data = '';
        echo $util->json_message($status, $message, $data);
    }
});

$app->run();


