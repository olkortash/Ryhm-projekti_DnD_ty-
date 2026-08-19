<?php
session_start();
set_include_path(dirname(__FILE__) . '/../');

$route = explode("?", $_SERVER["REQUEST_URI"])[0];
$method = strtolower($_SERVER["REQUEST_METHOD"]);

require_once 'libraries/auth.php';
require_once 'controllers/userManagement.php';
require_once 'controllers/characterManagement.php';

switch($route) {
    case "/":
        viewArticlesController();
    break;

    case "/register":
        registerController();
    break;

    case "/login":
        loginController();
    break;

    case "/logout":
        logoutController();
    break;

    case "/add_character":
      if(isLoggedIn()){
        addCharacterController();
      } else {
        loginController();
      }
    break;

    case "/delete_character":
      if(isLoggedIn()){
        deleteCharacterController();
      } else {
        loginController();
      }
    break;

    case "/update_character":
      if(isLoggedIn()){
        if($method == "get"){
          editCharacterController();  
        } else {
          updateCharacterController();
        }
      } else {
        loginController();
      }
    break;

    default:
      echo "404";
  }