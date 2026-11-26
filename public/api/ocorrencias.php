<?php
// Ocorrências ativas em todo o país (com as do concelho de Tábua assinaladas), a partir da API do Fogos.pt (dados da ANEPC).
//
// A chave de acesso NÃO fica no site: guarde-a num ficheiro «fogos-chave.txt» na pasta
// principal da conta do cPanel (a pasta acima de public_html). Sem esse ficheiro, este
// endereço responde {"ativo": false} e a caixa no site fica escondida.
//
// As respostas são guardadas 2 minutos para não sobrecarregar o Fogos.pt.
// Para ver o estado do servidor (sem mostrar a chave): /api/ocorrencias.php?diagnostico=1
//
// Escrito para funcionar em PHP 5.4 ou mais recente, com ou sem as extensões curl e mbstring.

@ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

// Se algo falhar a meio, responde em JSON em vez de uma página em branco.
register_shutdown_function('bvt_ao_terminar');
function bvt_ao_terminar() {
    $e = error_get_last();
    if ($e && in_array($e['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR), true)) {
        if (!headers_sent() && function_exists('http_response_code')) http_response_code(500);
        echo json_encode(array('ativo' => false, 'erro' => 'php', 'detalhe' => $e['message'] . ' (linha ' . $e['line'] . ')'));
    }
}

define('BVT_CONCELHO', 'tabua');
define('BVT_API', 'https://api.fogos.pt/v2/incidents/active?all=1');
define('BVT_VALIDADE', 120); // segundos

function bvt_valor($lista, $chave, $omissao = '') {
    return (is_array($lista) && isset($lista[$chave])) ? $lista[$chave] : $omissao;
}

function bvt_responder($dados, $codigo = 200) {
    if (function_exists('http_response_code')) http_response_code($codigo);
    echo json_encode($dados);
    exit;
}

function bvt_normalizar($t) {
    $t = strtr(trim((string) $t), array(
        'Á' => 'a', 'À' => 'a', 'Â' => 'a', 'Ã' => 'a', 'É' => 'e', 'Ê' => 'e', 'Í' => 'i', 'Ó' => 'o',
        'Ô' => 'o', 'Õ' => 'o', 'Ú' => 'u', 'Ç' => 'c', 'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a',
        'é' => 'e', 'ê' => 'e', 'í' => 'i', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ú' => 'u', 'ç' => 'c',
    ));
    return strtolower($t);
}

function bvt_pedir($url, $chave) {
    $cabecalhos = array(
        'Accept: application/json',
        'X-API-Key: ' . $chave,
        'User-Agent: BombeirosVoluntariosTabua-site/1.0 (+https://bvtabua.pt)',
    );
    if (function_exists('curl_init')) {
        $c = curl_init($url);
        curl_setopt_array($c, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $cabecalhos,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
        ));
        $corpo = curl_exec($c);
        $codigo = (int) curl_getinfo($c, CURLINFO_HTTP_CODE);
        $falha = curl_error($c);
        curl_close($c);
        return array($corpo === false ? '' : $corpo, $codigo, $falha);
    }
    if (ini_get('allow_url_fopen')) {
        $contexto = stream_context_create(array('http' => array(
            'method' => 'GET', 'timeout' => 8, 'ignore_errors' => true,
            'header' => implode("\r\n", $cabecalhos) . "\r\n",
        )));
        $corpo = @file_get_contents($url, false, $contexto);
        $codigo = 0;
        if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m)) $codigo = (int) $m[1];
        return array($corpo === false ? '' : $corpo, $codigo, $corpo === false ? 'file_get_contents falhou' : '');
    }
    return array('', 0, 'sem curl nem allow_url_fopen');
}

$raiz = isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] !== '' ? $_SERVER['DOCUMENT_ROOT'] : dirname(dirname(__FILE__));
$base = dirname(rtrim($raiz, '/'));
$ficheiroChave = $base . '/fogos-chave.txt';
$ficheiroCache = $base . '/.cache-fogos-v3.json';

$chave = is_readable($ficheiroChave) ? trim((string) file_get_contents($ficheiroChave)) : '';
// Remove carateres invisíveis que alguns editores acrescentam (BOM, quebras de linha).
$chave = preg_replace('/[^\x21-\x7E]/', '', $chave);

if (isset($_GET['diagnostico'])) {
    $teste = $chave !== '' ? bvt_pedir(BVT_API, $chave) : array('', 0, 'sem chave');
    $json = $teste[0] !== '' ? json_decode($teste[0], true) : null;
    bvt_responder(array(
        'php' => PHP_VERSION,
        'curl' => function_exists('curl_init'),
        'allow_url_fopen' => (bool) ini_get('allow_url_fopen'),
        'chave_na_pasta_principal' => is_file($ficheiroChave),
        'chave_encontrada' => $chave !== '',
        'chave_carateres' => strlen($chave),
        'cache_pode_escrever' => is_writable($base),
        'fogos_codigo_http' => $teste[1],
        'fogos_erro' => $teste[2],
        'fogos_resposta_valida' => is_array($json) && isset($json['data']),
        'fogos_inicio_resposta' => is_array($json) && isset($json['data']) ? '' : substr($teste[0], 0, 200),
    ));
}

if ($chave === '') {
    bvt_responder(array('ativo' => false));
}

// Resposta guardada ainda válida?
if (is_readable($ficheiroCache) && time() - filemtime($ficheiroCache) < BVT_VALIDADE) {
    echo file_get_contents($ficheiroCache);
    exit;
}

$pedido = bvt_pedir(BVT_API, $chave);
$json = $pedido[0] !== '' ? json_decode($pedido[0], true) : null;

if (!is_array($json) || !isset($json['data']) || !is_array($json['data'])) {
    // Falhou: devolve a última resposta guardada, se houver.
    if (is_readable($ficheiroCache)) {
        echo file_get_contents($ficheiroCache);
        exit;
    }
    bvt_responder(array('ativo' => true, 'erro' => true, 'ocorrencias' => array()), 502);
}

$ocorrencias = array();
foreach ($json['data'] as $o) {
    if (!is_array($o)) continue;
    $cor = (string) bvt_valor($o, 'statusColor');
    $inicio = bvt_valor($o, 'dateTime', array());
    $ocorrencias[] = array(
        'tabua' => bvt_normalizar(bvt_valor($o, 'concelho')) === BVT_CONCELHO,
        'distrito' => (string) bvt_valor($o, 'district'),
        'concelho' => (string) bvt_valor($o, 'concelho'),
        'natureza' => (string) bvt_valor($o, 'natureza'),
        'estado' => (string) bvt_valor($o, 'status'),
        'cor' => preg_match('/^[0-9A-Fa-f]{6}$/', $cor) ? $cor : '',
        'freguesia' => (string) bvt_valor($o, 'freguesia'),
        'localidade' => (string) bvt_valor($o, 'localidade'),
        'data' => (string) bvt_valor($o, 'date'),
        'hora' => (string) bvt_valor($o, 'hour'),
        'inicio' => (int) bvt_valor($inicio, 'sec', 0),
        'operacionais' => (int) bvt_valor($o, 'man', 0),
        'terrestres' => (int) bvt_valor($o, 'terrain', 0),
        'aereos' => (int) bvt_valor($o, 'aerial', 0),
        'incendio' => (bool) bvt_valor($o, 'isFire', false),
        'importante' => (bool) bvt_valor($o, 'important', false),
        'lat' => round((float) bvt_valor($o, 'lat', 0), 5),
        'lng' => round((float) bvt_valor($o, 'lng', 0), 5),
    );
}

function bvt_mais_recente($a, $b) {
    return $b['inicio'] - $a['inicio'];
}
usort($ocorrencias, 'bvt_mais_recente');

$resultado = json_encode(array(
    'ativo' => true,
    'atualizado' => time(),
    'ocorrencias' => $ocorrencias,
));

@file_put_contents($ficheiroCache, $resultado, LOCK_EX);
echo $resultado;
