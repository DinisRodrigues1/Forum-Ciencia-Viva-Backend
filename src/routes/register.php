<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->add(function ($req, $res, $next){
    $response = $next($req, $res);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});


$app->post('/api/user/register', function(Request $request, Response $response){
    $nome = $request->getParam('nome_user');
    $username = $request->getParam('username');
    $email = $request->getParam('email');
    $dataNasc = $request->getParam('data_nasc');
    $foto = $request->getParam('foto');
    $password = $request->getParam('password_hash');
    $descricao = $request->getParam('descricao');
    $prefs = $request->getParam('pref1');
    $prefs2 = $request->getParam('pref2');
    $prefs3 = $request->getParam('pref3');
    $prefs4 = $request->getParam('pref4');
    $prefs5 = $request->getParam('pref5');
    $prefs6 = $request->getParam('pref6');
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $token = bin2hex(openssl_random_pseudo_bytes(16));

    $sql = "INSERT INTO users (nome_user, username, email, data_nasc, foto, password_hash, descricao, preferencias, token, preferencias2,
preferencias3, preferencias4, preferencias5, preferencias6) VALUES 
      (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
//READICIONAR FOTO NA QUERY E TIRAR DE COMMENT, MESMO PARA PREFERENCIAS
    try{
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
        $stmt->bindParam(8, $prefs, PDO::PARAM_BOOL);
        $stmt->bindParam(9, $token, PDO::PARAM_STR);
        $stmt->bindParam(10, $prefs2, PDO::PARAM_BOOL);
        $stmt->bindParam(11, $prefs3, PDO::PARAM_BOOL);
        $stmt->bindParam(12, $prefs4, PDO::PARAM_BOOL);
        $stmt->bindParam(13, $prefs5, PDO::PARAM_BOOL);
        $stmt->bindParam(14, $prefs6, PDO::PARAM_BOOL);




        $stmt->execute();
        echo '{"notice": {"text": "Utilizador Criado"}';

    }catch(PDOException $e){
        echo '{"error": {"text:" '.$e->getMessage().'}';
    }

});