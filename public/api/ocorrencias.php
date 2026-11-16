<?php
// Ocorrências ativas no concelho de Tábua, a partir da API do Fogos.pt (dados da ANEPC).
//
// A chave de acesso NÃO fica no site: guarde-a num ficheiro «fogos-chave.txt» na pasta
// principal da conta do cPanel (a pasta acima de public_html). Sem esse ficheiro, este
// endereço responde {"ativo": false} e a caixa no site fica escondida.
//
// As respostas são guardadas 2 minutos para não sobrecarregar o Fogos.pt.

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

const CONCELHO = 'Tábua';
const API = 'https://api.fogos.pt/v2/incidents/active?all=1';
const VALIDADE = 120; // segundos

$base = dirname($_SERVER['DOCUMENT_ROOT'] ?? __DIR__ . '/../..');
$ficheiroChave = $base . '/fogos-chave.txt';
$ficheiroCache = $base . '/.cache-fogos-tabua.json';

function responder(array $dados, int $codigo = 200): void {
    http_response_code($codigo);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function normalizar(string $t): string {
    $t = mb_strtolower(trim($t), 'UTF-8');
    return strtr($t, ['á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'é' => 'e', 'ê' => 'e', 'í' => 'i',
        'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ú' => 'u', 'ç' => 'c']);
}

$chave = is_readable($ficheiroChave) ? trim((string) file_get_contents($ficheiroChave)) : '';
if ($chave === '') {
    responder(['ativo' => false]);
}

// Resposta guardada ainda válida?
if (is_readable($ficheiroCache) && time() - filemtime($ficheiroCache) < VALIDADE) {
    echo file_get_contents($ficheiroCache);
    exit;
}

$contexto = stream_context_create([
    'http' => [
        'method' => 'GET',
        'timeout' => 8,
        'ignore_errors' => true,
        'header' => "Accept: application/json\r\n" .
            "X-API-Key: {$chave}\r\n" .
            "User-Agent: BombeirosVoluntariosTabua-site/1.0 (+https://bvtabua.pt)\r\n",
    ],
]);
$corpo = @file_get_contents(API, false, $contexto);
$json = $corpo ? json_decode($corpo, true) : null;

if (!is_array($json) || !isset($json['data']) || !is_array($json['data'])) {
    // Falhou: devolve a última resposta guardada, se houver.
    if (is_readable($ficheiroCache)) {
        echo file_get_contents($ficheiroCache);
        exit;
    }
    responder(['ativo' => true, 'erro' => true, 'ocorrencias' => []], 502);
}

$alvo = normalizar(CONCELHO);
$ocorrencias = [];
foreach ($json['data'] as $o) {
    if (!is_array($o) || normalizar((string) ($o['concelho'] ?? '')) !== $alvo) continue;
    $ocorrencias[] = [
        'natureza' => (string) ($o['natureza'] ?? ''),
        'estado' => (string) ($o['status'] ?? ''),
        'cor' => preg_match('/^[0-9A-Fa-f]{6}$/', (string) ($o['statusColor'] ?? '')) ? $o['statusColor'] : '',
        'freguesia' => (string) ($o['freguesia'] ?? ''),
        'localidade' => (string) ($o['localidade'] ?? ''),
        'data' => (string) ($o['date'] ?? ''),
        'hora' => (string) ($o['hour'] ?? ''),
        'inicio' => (int) ($o['dateTime']['sec'] ?? 0),
        'operacionais' => (int) ($o['man'] ?? 0),
        'terrestres' => (int) ($o['terrain'] ?? 0),
        'aereos' => (int) ($o['aerial'] ?? 0),
        'incendio' => !empty($o['isFire']),
    ];
}
usort($ocorrencias, fn($a, $b) => $b['inicio'] <=> $a['inicio']);

$resultado = json_encode([
    'ativo' => true,
    'atualizado' => time(),
    'ocorrencias' => $ocorrencias,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

@file_put_contents($ficheiroCache, $resultado, LOCK_EX);
echo $resultado;
