<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


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

//Add User - NECESSARIO ADICIONAR FORM PARA ADICIONAR OS UTILIZADORES
$app->post('/api/users/add', function(Request $request, Response $response){
    $nome = $request->getParam('nome_user');
    $username = $request->getParam('username');
    $email = $request->getParam('email');
    $dataNasc = $request->getParam('data_nasc');
    $foto = $request->getParam('foto');
    $password = $request->getParam('password_hash');
    $descricao = $request->getParam('descricao');
    

    $sql = "INSERT INTO users (nome_user, username, email, data_nasc, foto, password_hash, descricao) VALUES 
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
        $stmt->bindParam(':dataNasc', $dataNasc);
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
    $dataNasc = $request->getParam('data_nasc');
    $foto = $request->getParam('foto');
    $password = $request->getParam('password_hash');
    $descricao = $request->getParam('descricao');
    $perfil = $request ->getParam('perfis_id_perfis');

    $sql = "UPDATE users SET
              nome_user = :nome,
              username = :username,
              email = :email,
              data_nasc = :dataNasc,
              foto = :foto,
              password_hash = :password,
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
        $stmt->bindParam(':password', $password);
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
