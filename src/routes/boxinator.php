<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Headers to allow local origin.

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});


//
// Countries
//


// Get all countries.

$app->get('/api/boxinator/countries', function (Request $request, Response $response){

    $sql = "SELECT * FROM countries";

    try{
        
        $db = new db();

        $db = $db->connect();

        $stmt = $db->query($sql);
        $countries = $stmt->fetchALL(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($countries);

    }catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

// Get Country by id

$app->get('/api/boxinator/country/{id}', function (Request $request, Response $response){

    $id = $request->getAttribute('id');
    
    
    $sql = "SELECT * FROM countries WHERE id_countries = $id";
    
        try{
            
            $db = new db();
    
            $db = $db->connect();
    
            $stmt = $db->query($sql);
            $country = $stmt->fetchALL(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($country);
    
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        } 
    });


// Get packages

$app->get('/api/boxinator/packages', function (Request $request, Response $response){

    $sql = "SELECT * FROM package";

    try{
        
        $db = new db();

        $db = $db->connect();

        $stmt = $db->query($sql);
        $packages = $stmt->fetchALL(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($packages);

    }catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

// Get Package by id

$app->get('/api/boxinator/package/{id}', function (Request $request, Response $response){

$id = $request->getAttribute('id');

$sql = "SELECT * FROM package WHERE id_package = $id";

    try{
        
        $db = new db();

        $db = $db->connect();

        $stmt = $db->query($sql);
        $package = $stmt->fetchALL(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($package);

    }catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});


// Get Packages for latest shipment
// Includes all packages with attached country and shipment

$app->get('/api/boxinator/packages/get', function (Request $request, Response $response){

    
    $sql = "SELECT * FROM package INNER JOIN shipment on shipment.id_shipment = package.shipment_id INNER JOIN countries on package.country_id=countries.id_countries WHERE package.shipment_id = (SELECT id_shipment FROM ( SELECT * FROM shipment ORDER BY id_shipment DESC LIMIT 1) sub ORDER BY id_shipment ASC)  ORDER BY package.id_package DESC";
        try{
            
            $db = new db();
    
            $db = $db->connect();
    
            $stmt = $db->query($sql);
            $package = $stmt->fetchALL(PDO::FETCH_OBJ);
            $db = null;
            echo json_encode($package);
    
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
    });

// Starts a new Shipment

$app->post('/api/boxinator/shipment/add', function (Request $request, Response $response){

    $shipdate = date("Y-m-d");
    $total_weight = 0;
    $total_cost = 0;

    
    $sql = "INSERT INTO `shipment` (`shipdate`, `total_weight`, `total_cost`) VALUES (:shipdate, :total_weight, :total_cost)";
    
        try{
            
            $db = new db();
    
            $db = $db->connect();
    
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':shipdate',    $shipdate);
            $stmt->bindParam(':total_weight',    $total_weight);
            $stmt->bindParam(':total_cost',    $total_cost);

            $stmt->execute();

            $lastId = $db->lastInsertId();

            echo $lastId;
    
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
    });

    // Gets the information of the latest shipment.

    $app->get('/api/boxinator/shipment/get', function (Request $request, Response $response){

    
        
        $sql = "SELECT * FROM ( SELECT * FROM shipment ORDER BY id_shipment DESC LIMIT 1) sub ORDER BY id_shipment ASC";
        
            try{
                
                $db = new db();
        
                $db = $db->connect();
        

    
                $stmt = $db->query($sql);
                $shipment = $stmt->fetchALL(PDO::FETCH_OBJ);
                
    
                echo json_encode($shipment);
        
            }catch(PDOException $e){
                echo '{"error": {"text": '.$e->getMessage().'}';
            }
        });

// Updates Shipment e.g. when a new package is added.

function updateShipment($shipid){

    $id = $shipid;
    $shipdate = date("Y-m-d");
    // Calculates the total cost.
    $sql = "SELECT SUM(`package`.`weight` * `countries`.`multiplier`) AS total_cost FROM `package`, `countries` WHERE `countries`.`id_countries` = `package`.`country_id` AND `package`.`shipment_id` = $id";
    
        try{
            

            $db = new db();

            $db = $db->connect();
    
            $stmt = $db->query($sql);
            $sum_cost = $stmt->fetch();
            $total_cost = $sum_cost['total_cost'];
            $db = null;

    
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }
        //Calculates the total weight
        $sql = "SELECT SUM(`package`.`weight`) AS total_weight FROM `package`, `countries` WHERE `countries`.`id_countries` = `package`.`country_id` AND `package`.`shipment_id` = $id";
    
        try{
            
            $db = new db();

            $db = $db->connect();
    
            $stmt = $db->query($sql);
            $sum_weight = $stmt->fetch();
            $total_weight = $sum_weight['total_weight'];
            $db = null;

    
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }

        //Inserts date, total weight and cost to current shipment.
    $sql = "UPDATE `shipment` SET 
        shipdate = :shipdate, 
        total_weight = :total_weight, 
        total_cost = :total_cost 
        WHERE id_shipment = $id";

        try{

            $db = new db();

            $db = $db->connect();

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':shipdate', $shipdate);
            $stmt->bindParam(':total_weight', $total_weight);
            $stmt->bindParam(':total_cost', $total_cost);

            $stmt->execute();

            }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }

    };


// Add new package to latest shipment

$app->post('/api/boxinator/package/add', function (Request $request, Response $response){

    $name = $request->getParam('name');
    $weight = $request->getParam('weight');
    $red = $request->getParam('red');
    $green = $request->getParam('green');
    $blue = $request->getParam('blue');
    $country_id = $request->getParam('country_id');

    // Calculates cost based on weight and country.
    $sql = "SELECT SUM($weight * `countries`.multiplier) AS cost FROM `countries` WHERE `countries`.`id_countries` = $country_id";
    
    try{
        
        $db = new db();

        $db = $db->connect();

        $stmt = $db->query($sql);
        $get_cost = $stmt->fetch();
        $cost = $get_cost['cost'];
        $db = null;


    }catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
    // Gets the latest shipment id
    $sql = "SELECT id_shipment AS shipid FROM ( SELECT * FROM shipment ORDER BY id_shipment DESC LIMIT 1) sub ORDER BY id_shipment ASC";
    
    try{
        
        $db = new db();

        $db = $db->connect();

        $stmt = $db->query($sql);
        $get_shipid = $stmt->fetch();
        $shipment_id = $get_shipid['shipid'];
        $db = null;


    }catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }

    // Inserts package information.
    $sql = "INSERT INTO `package` (`shipment_id`, `name`, `weight`, `cost`, `red`, `green`, `blue`, `country_id`) VALUES (:shipment_id, :name, :weight, :cost,:red, :green, :blue, :country_id)";
    
        try{
            
            $db = new db();
    
            $db = $db->connect();
    
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':shipment_id',    $shipment_id);
            $stmt->bindParam(':name',           $name);
            $stmt->bindParam(':weight',         $weight);
            $stmt->bindParam(':cost',           $cost);
            $stmt->bindParam(':red',            $red);
            $stmt->bindParam(':green',          $green);
            $stmt->bindParam(':blue',           $blue);
            $stmt->bindParam(':country_id',     $country_id);

            $stmt->execute();

            $lastInsertId = $db->lastInsertId();
            
    
        }catch(PDOException $e){
            echo '{"error": {"text": '.$e->getMessage().'}';
        }

        updateShipment($shipment_id);
        getboxid($shipment_id);

    });

    // Sends a reply with the latest added package information.
    function getboxid($id){
    
        $sql = "SELECT * FROM package INNER JOIN shipment on shipment.id_shipment = package.shipment_id INNER JOIN countries on package.country_id=countries.id_countries WHERE package.shipment_id = $id ORDER BY package.id_package DESC";
        
            try{
                
                $db = new db();
        
                $db = $db->connect();
        
                $stmt = $db->query($sql);
                $package = $stmt->fetchALL(PDO::FETCH_OBJ);
                $db = null;
                echo json_encode($package);
        
            }catch(PDOException $e){
                echo '{"error": {"text": '.$e->getMessage().'}';
            }
        };