<?php
session_start();

//auto load needed classes
spl_autoload_register(function ($class) {
    require_once 'classes/'.$class.'.php';
});
