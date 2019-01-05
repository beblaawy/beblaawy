<?php

namespace Zarboxa\Core\Framework;

class Zarboxa{


    public static function bootstrap(){

    }

    public static function init(){

        /* Define path constants */
        define("DS", DIRECTORY_SEPARATOR);

        define("ROOT_PATH", substr(getcwd(),0,-6));

        define("CONFIG_PATH", ROOT_PATH . "config" . DS);

        /*
        * env should be config before config/app.php file
        */
        self::envData();

        /*
        * debug mode
        */
        if (env('DEBUG_MODE') && env('DEBUG_MODE') == 'true') {
            error_reporting(-1);
            ini_set('display_errors', 'On');
        }

        

        /*
        * Get the user defined constants
        */
        require_once CONFIG_PATH . 'app.php';

        foreach ($data as $key => $value) {
            define($key, $value);
        }

        define("APP_PATH", ROOT_PATH . 'APP_FOLDER' . DS);
        
        define("PUBLIC_PATH", ROOT_PATH . "PUBLIC_FOLDER" . DS);

        define("VIEW_PATH", ROOT_PATH . VIEW_FOLDER . DS);

        define("VENDOR_PATH", ROOT_PATH . VENDOR_FOLDER . DS);



        define("FRAMEWORK_PATH", ROOT_PATH . "framework" . DS);

        define("CONTROLLER_PATH", ROOT_PATH . "controllers" . DS);

        define("MODEL_PATH", ROOT_PATH . "models" . DS);


        define("CORE_PATH", FRAMEWORK_PATH . "core" . DS);

        define('DB_PATH', FRAMEWORK_PATH . "database" . DS);

        define("LIB_PATH", FRAMEWORK_PATH . "libraries" . DS);

        define("HELPER_PATH", FRAMEWORK_PATH . "helpers" . DS);

        define("UPLOAD_PATH", PUBLIC_PATH . "uploads" . DS);


        // Define platform, controller, action, for example:

        // index.php?p=admin&c=Goods&a=add

        define("PLATFORM", isset($_REQUEST['p']) ? $_REQUEST['p'] : 'home');

        define("CONTROLLER", isset($_REQUEST['c']) ? $_REQUEST['c'] : 'Index');

        define("ACTION", isset($_REQUEST['a']) ? $_REQUEST['a'] : 'index');


        define("CURR_CONTROLLER_PATH", CONTROLLER_PATH . PLATFORM . DS);

        define("CURR_VIEW_PATH", VIEW_PATH . PLATFORM . DS);


        // Load core classes

        // require CORE_PATH . "Controller.class.php";

        // require CORE_PATH . "Loader.class.php";

        // require DB_PATH . "Mysql.class.php";

        // require CORE_PATH . "Model.class.php";


        // Load configuration file

        // $GLOBALS['config'] = include CONFIG_PATH . "config.php";


        // Start session

        // require ROOT_PATH . 'app/helpers.php';

        session_start();
    }

    public static function envData(){
        if (file_exists(ROOT_PATH . ".env")) {
            $data = [];
            $handle = fopen(ROOT_PATH . ".env", "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $sub = explode("=", $line);
                    if (isset($sub[1])) {
                        $data[ $sub[0] ] = trim($sub[1]);
                    }
                }
                fclose($handle);
            } else {
                // error opening the file.
            }
            define("ENV_DATA", $data);
        }
    }
}