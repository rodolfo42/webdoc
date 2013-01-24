<?php
define("BASE_DIR", dirname(__FILE__));
set_include_path(get_include_path().PATH_SEPARATOR.BASE_DIR.DIRECTORY_SEPARATOR."lib");
set_include_path(get_include_path().PATH_SEPARATOR.BASE_DIR.DIRECTORY_SEPARATOR."app");

require_once "Slim/Slim.php";
\Slim\Slim::registerAutoloader();

require_once "autoloader.php";

// MISC
function d($var) {
	echo "<pre>";
	var_dump($var);
	echo "</pre>";
	die();
}

// PERSISTENCIA
$db = new \Database\Database("localhost", "root", "", "webdoc");
$documento = new \Model\Documento($db);

// APP
$app = new \Slim\Slim;

function renderJson($something, $headers) {
	$headers["Content-type"] = "application/json; charset=UTF-8";
	echo json_encode($something);
};

function error($message) {
	return array('error' => compact('message'));
}

function success($message) {
    return array('success' => compact('message'));
}

$app->get("/documentos", function() use($app, $documento) {
    $result = $documento->findAll();
    renderJson($result, $app->response());
});

$app->post("/documentos", function() use($app, $documento) {
    $data = $app->request()->getBody();
    $doc = json_decode($data, true, JSON_HEX_APOS);
    $result = $documento->addNew($doc);
    if($result) {
        $result = array('id' => $result);
    } else {
        $result = error("não foi possível adicionar o documento, verifique os logs");
    }
    renderJson($result, $app->response());
});

$app->put("/documentos/:id", function($id) use($app, $documento) {
    $data = $app->request()->getBody();
    $doc = json_decode($data, true, JSON_HEX_APOS);

    $result = $documento->update($id, $doc);

    if($result) {
        $result = success("documento salvo com sucesso");
    } else {
        $result = error("não foi possível salvar o documento, verifique os logs");
    }

    renderJson($result, $app->response());
});

$app->run();