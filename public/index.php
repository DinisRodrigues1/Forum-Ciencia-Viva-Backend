<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\App;
use Slim\Http\UploadedFile;
use Slim\Middleware\TokenAuthentication;


require '../vendor/autoload.php';
require '../src/config/db.php';

$config = [
    'settings' => [
        'displayErrorDetails' => true
    ]
];

$app = new App($config);

$app->options('/(routes:.*)', function ($request, $response, $args){
    return $response;

});

$app->add(function ($req, $res, $next){
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});

$authenticator = function($request, TokenAuthentication $tokenAuth){

    # Search for token on header, parameter, cookie or attribute
    $token = $tokenAuth->findToken($request);

    # Your method to make token validation
    $auth = new \app\Auth();

    # If occured ok authentication continue to route autenticação feita - segue caminho
    # before end you can storage the user informations or whatever guardar info user
    $auth->getUserByToken($token);

};


$app->add(new \Slim\Middleware\TokenAuthentication([
    'path' => '/restrict',
    'authenticator' => $authenticator
]));

$app->get('/', function($request, $response){
   return $response->withJson('It is public area', 200, JSON_PRETTY_PRINT);
});

$app->get('/restrict', function($request, $response){
    return $response->withJson('Restricted Area. Token Authentication works!', 200, JSON_PRETTY_PRINT);
});

// Get All Customers
$app->get('/api/users', function(Request $request, Response $response){
    $sql = "SELECT * FROM users";

    try{
        //Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->query($sql);
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($users);
    }catch(PDOException $e){
        echo '{"error": {"text:" '.$e->getMessage().'}';
    }

});
// Get Single User
$app->get('/api/users/{id_user}', function(Request $request, Response $response){
    $id = $request->getAttribute('id_user');

    $sql = "SELECT * FROM users WHERE id_user = $id";

    try{
        //Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->query($sql);
        $user = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($user);
    }catch(PDOException $e){
        echo '{"error": {"text:" '.$e->getMessage().'}';
    }
});

//Add User
$app->post('/api/users/add', function(Request $request, Response $response){
    $nome = $request->getParam('nome_user');
    $username = $request->getParam('username');
    $email = $request->getParam('email');
    $dataNasc = $request->getParam('dat_nasc');
    $foto = $request->getParam('foto');
    $password = $request->getParam('password_hash');
    $descricao = $request->getParam('descricao');


    $sql = "INSERT INTO users (nome_user, username, email, dat_nasc, foto, password_hash, descricao) VALUES 
      (:nome, :username, :email, :dataNasc, :foto, :password, :descricao)";

    try{
        //Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':datNasc', $dataNasc);
        $stmt->bindParam(':foto', $foto);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':descricao', $descricao);

        $stmt->execute();
        echo '{"notice": {"text": "Utilizador Criado"}';

    }catch(PDOException $e){
        echo '{"error": {"text:" '.$e->getMessage().'}';
    }

});

$app->put('/api/users/update/{id_user}', function(Request $request, Response $response){
    $id = $request->getAttribute('id_user');
    $nome = $request->getParam('nome_user');
    $username = $request->getParam('username');
    $email = $request->getParam('email');
    $dataNasc = $request->getParam('dat_nasc');
    $foto = $request->getParam('foto');
    $descricao = $request->getParam('descricao');
    $perfil = $request ->getParam('perfis_id_perfis');

    $sql = "UPDATE users SET
              nome_user = :nome,
              username = :username,
              email = :email,
              dat_nasc = :dataNasc,
              foto = :foto,
              descricao = :descricao,
              perfis_id_perfis = :perfil
           WHERE id_user = $id";

    try{
        //Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':dataNasc', $dataNasc);
        $stmt->bindParam(':foto', $foto);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':perfil',$perfil);

        $stmt->execute();
        echo '{"notice": {"text": "Utilizador Atualizado"}';

    }catch(PDOException $e){
        echo '{"error": {"text:" '.$e->getMessage().'}';
    }

});
// Delete User
$app->delete('/api/users/delete/{id_user}', function(Request $request, Response $response) {
    $id = $request->getAttribute('id_user');

    $sql = "DELETE FROM users WHERE id_user = $id";

    try {
        //Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;

        echo '{"notice": {"text": "Utilizador Apagado"}';

    } catch (PDOException $e) {
        echo '{"error": {"text:" ' . $e->getMessage() . '}';
    }
});
//UPLOAD IMAGE
$container = $app->getContainer();
$container['upload_directory'] = __DIR__ . '../files/';

$app->post('/api/upload', function (Request $request, Response  $response) use ($app) {

    $directory = $this->get('upload_directory');

    $uploadedFiles = $request->getParam('ficheiro');

    $uploadedFile = $uploadedFiles;
    if($uploadedFile->getError() === UPLOAD_ERR_OK) {
        $filename = moveUploadedFile($directory, $uploadedFile);
        $response->write('uploaded ' . $filename . '<br/>');
    }

});


function moveUploadedFile($directory, UploadedFile $uploadedFile){
    $extension = pathinfo($uploadedFile->getClientFilename(),
        PATHINFO_EXTENSION);
    $basename = bin2hex(openssl_random_pseudo_bytes(8));
    $filename = sprintf('%s.%0.8s', $basename, $extension);
    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

    return $filename;
}
//Register User
$app->post('/api/user/register', function(Request $request, Response $response) {
    $nome = $request->getParam('nome_user');
    $username = $request->getParam('username');
    $email = $request->getParam('email');
    $dataNasc = $request->getParam('dat_nasc');
    $foto = $request->getParam('foto');
    $password = $request->getParam('password_hash');
    $descricao = $request->getParam('descricao');
    //$preferencias = $request->getParam('preferencias');

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $token = bin2hex(openssl_random_pseudo_bytes(16));

    $sql = "INSERT INTO users (nome_user, username, email, dat_nasc, foto, password_hash, descricao, token) VALUES 
      (?, ?, ?, ?, ?, ?, ?, ?) /*INNER JOIN users_has_pref ON id_user = users_id_user 
      INNER JOIN preferencias ON areas_id_area = id_area*/";
//READICIONAR FOTO NA QUERY E TIRAR DE COMMENT, MESMO PARA PREFERENCIAS
    try {
        //Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $nome, PDO::PARAM_STR);
        $stmt->bindParam(2, $username, PDO::PARAM_STR);
        $stmt->bindParam(3, $email, PDO::PARAM_STR);
        $stmt->bindParam(4, $dataNasc, PDO::PARAM_STR);
        $stmt->bindParam(5, $foto, PDO::PARAM_STR);
        $stmt->bindParam(6, $hash, PDO::PARAM_STR);
        $stmt->bindParam(7, $descricao, PDO::PARAM_STR);
        $stmt->bindParam(8, $token, PDO::PARAM_STR);
        //$stmt->bindParam(9,$preferencia, PDO::PARAM_STR);


        $stmt->execute();
        echo '{"notice": {"text": "Utilizador Criado"}';

    } catch (PDOException $e) {
        echo '{"error": {"text:" ' . $e->getMessage() . '}';
    }
});
//GET USER BY TOKEN
$app->get('/api/users/auth/{token}', function(Request $request, Response $response) {
    $iden = $request->getAttribute('token');

    $sql = "SELECT * FROM users WHERE token = $iden";

    try {
        //Get DB Object
        $db = new \db();
        // Connect
        $db = $db->connect();

        $stmt = $db->query($sql);
        $user = $stmt->fetchAll(\PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($user);
    } catch (\PDOException $e) {
        echo '{"error": {"text:" ' . $e->getMessage() . '}';
    }
});
//GET ALL POSTS
$app->get('/api/threads', function(Request $request, Response $response){

    $sql = "SELECT * FROM posts_threads";
    try {
        //Get DB Object
        $db = new\db();
        // Connect
        $db = $db->connect();

        $stmt = $db->query($sql);
        $post = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($post);
    } catch (\PDOException $e) {
        echo '{"error": {"text:" ' . $e->getMessage() . '}';
    }
});
//GET ALL AREAS
$app->get('/api/areas', function(Request $request, Response $response){

    $sql = "SELECT * FROM preferencias";
    try {
        //Get DB Object
        $db = new\db();
        // Connect
        $db = $db->connect();

        $stmt = $db->query($sql);
        $pref = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($pref);
    } catch (\PDOException $e) {
        echo '{"error": {"text:" ' . $e->getMessage() . '}';
    }
});
// Get Single Area
$app->get('/api/areas/{id_area}', function(Request $request, Response $response){
    $ident = $request->getAttribute('id_area');

    $sql = "SELECT * FROM preferencias WHERE id_area = $ident";

    try{
        //Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->query($sql);
        $pref = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($pref);
    }catch(PDOException $e){
        echo '{"error": {"text:" '.$e->getMessage().'}';
    }
});

//Add Thread
$app->post('/api/threads/add', function(Request $request, Response $response){
    $areaz = $request->getParam('id_area');
    $post = $request->getParam('post');
    $titulo = $request->getParam('titulo');


    $sql = "INSERT INTO posts_threads (posts_threads.post, posts_threads.users_id_user, posts_threads.titulo, 
			posts_threads.data_post)
            VALUES(?, 1, ?, now());
            SELECT LAST_INSERT_ID() AS ids INTO @myvar;
            INSERT INTO preferencias_has_posts_threads (preferencias_id_area, posts_threads_id_post)
			VALUES (?, @myvar);";

    try{
        //Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $post, PDO::PARAM_STR);
        $stmt->bindParam(2, $titulo, PDO::PARAM_STR);
        $stmt->bindParam(3, $areaz, PDO::PARAM_INT);

        $stmt->execute();
        echo '{"notice": {"text": "Thread Criado"}';

    }catch(PDOException $e){
        echo '{"error": {"text:" '.$e->getMessage().'}';
    }

});
//GET POST FROM AREA
$app->get('/api/threads/{id_area}', function(Request $request, Response $response){
    $area = $request->getAttribute('id_area');

    $sql = "SELECT * FROM posts_threads 
            INNER JOIN preferencias_has_posts_threads
            ON id_post = preferencias_has_posts_threads.posts_threads_id_post
            INNER JOIN preferencias ON id_area = preferencias_id_area 
            WHERE id_area = $area";
    try {
        //Get DB Object
        $db = new\db();
        // Connect
        $db = $db->connect();

        $stmt = $db->query($sql);
        $post = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($post);
    } catch (\PDOException $e) {
        echo '{"error": {"text:" ' . $e->getMessage() . '}';
    }
});
//GET POST BY ID
$app->get('/api/thread/{id_post}', function(Request $request, Response $response){
    $post_id = $request->getAttribute('id_post');

    $sql = "SELECT * FROM posts_threads 
            INNER JOIN preferencias_has_posts_threads
            ON id_post = preferencias_has_posts_threads.posts_threads_id_post
            WHERE posts_threads.posts_threads_id_post = $post_id";
    try {
        //Get DB Object
        $db = new\db();
        // Connect
        $db = $db->connect();

        $stmt = $db->query($sql);
        $post = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($post);
    } catch (\PDOException $e) {
        echo '{"error": {"text:" ' . $e->getMessage() . '}';
    }
});
//POST REPLY
$app->post('/api/threads/reply', function(Request $request, Response $response){
    $post = $request->getParam('post');
    $id = $request->getParam('id_post');


    $sql =  "INSERT INTO posts_threads (posts_threads.post, posts_threads.users_id_user, posts_threads.titulo,
            posts_threads.data_post, posts_threads.posts_threads_id_post)
            VALUES(?, 1, 'RE:', now(), ?);
            INSERT INTO preferencias_has_posts_threads (posts_threads_id_post, preferencias_id_area)
			VALUES (?, 6)";
    /* */
    try{
        //Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();

        $stmt = $db->prepare($sql);
        $stmt->bindParam(1, $post, PDO::PARAM_STR);
        $stmt->bindParam(2, $id, PDO::PARAM_INT);
        $stmt->bindParam(3, $id, PDO::PARAM_INT);

        $stmt->execute();
        echo '{"notice": {"text": "Resposta Enviada!"}';

    }catch(PDOException $e){
        echo '{"error": {"text:" '.$e->getMessage().'}';
    }

});
$app->run();



// Customer Routes
require '../src/routes/users.php';
require '../src/routes/register.php';

