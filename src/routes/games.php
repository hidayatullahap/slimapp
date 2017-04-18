<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Get All Games
$app->get('/api/games', function(Request $request, Response $response){
    //Query ke database
    $sql = "SELECT * FROM games";

    try{
        //Koneksi ke database dan jalankan query SQL
        $db = new db();
        $db = $db->connect();
        $stmt = $db->query($sql);
        
        //Cek apakah query memberikan kembalian data
        if($stmt->rowCount() > 0) {
            $games = $stmt->fetchAll(PDO::FETCH_OBJ);  //Gunakan fetchAll untuk fetch semua data
            return 
            $response
            ->withJson($games,200);//withJson hanya ada pada slim versi 3
        } else {
            return 
            $response->withJson($message->errorServerResponse(404),404);  //Memanggil kelas global errorMeesages untuk data tidak ditemukan
        }
        //Unregister pemanggilan DB 
        $db = null;
        //Lakukan catch exception pada error PDO
    } catch(PDOException $e){
        //Beri pesan error PDO
        echo '{"error": {"text": '.$e->getMessage().'}}';
    }

});

// Get Single Game
$app->get('/api/game/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $message = new errorMessages();

    $sql = "SELECT * FROM games WHERE id = $id";
    try{
        $db = new db();
        $db = $db->connect();
        $stmt = $db->query($sql);
        
        if($stmt->rowCount() > 0) {
            $games = $stmt->fetch(PDO::FETCH_OBJ);
            return 
            $response
            ->withJson($games,200);
            
        } else {
            return 
            $response->withJson($message->errorServerResponse(404),404);
        }

        $db = null;
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}}';
    }
});

// Add Game
$app->post('/api/game/add', function(Request $request, Response $response){
    $judul = $request->getParam('judul');
    $url_gambar = $request->getParam('url_gambar');

    $sql = "INSERT INTO games (judul,url_gambar) VALUES
    (:judul,:url_gambar)";

    try{
        $db = new db();
        $db = $db->connect();
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':judul', $judul);
        $stmt->bindParam(':url_gambar',  $url_gambar);

        $stmt->execute();

        echo '{"notice": {"text": "Game Added"}}';

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}}';
    }
});

// Update Game
$app->put('/api/game/update/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $judul = $request->getParam('judul');
    $url_gambar = $request->getParam('url_gambar');

    $sql = "UPDATE games SET
				judul 	    = :judul,
				url_gambar 	= :url_gambar
			WHERE id = $id";

    try{
        $db = new db();
        $db = $db->connect();

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':judul', $judul);
        $stmt->bindParam(':url_gambar',  $url_gambar);

        $stmt->execute();

        echo '[{"notice": {"text": "Game Updated"}}]';

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}}';
    }
});

// Delete Game
$app->delete('/api/game/delete/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');

    $sql = "DELETE FROM games WHERE id = $id";

    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Game Deleted"}}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}}';
    }
});

//$app->run();