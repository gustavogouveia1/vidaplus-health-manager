<?php
session_start();

// Configurações de Caminhos
define('BASE_PATH', __DIR__);
define('VIEW_PATH', BASE_PATH . '/app/Views/');
define('DB_DIR', BASE_PATH . '/app/Database/'); // Pasta dos JSONs
define('DB_FILE', DB_DIR . 'data.json'); // Consultas

// --- FUNÇÕES HELPER (MOCK DE BANCO DE DADOS) ---

// Função genérica para ler qualquer JSON
if (!function_exists('getJsonData')) {
    function getJsonData($filename) {
        $path = DB_DIR . $filename;
        if (!file_exists($path)) {
            // Se não existir, retorna array vazio
            return []; 
        }
        $content = file_get_contents($path);
        return json_decode($content, true) ?: [];
    }
}

// Ler Consultas (Específico para o arquivo de dados principal)
if (!function_exists('getDbData')) {
    function getDbData() {
        if (!file_exists(DB_FILE)) {
            if (!is_dir(dirname(DB_FILE))) mkdir(dirname(DB_FILE), 0777, true);
            file_put_contents(DB_FILE, json_encode(['consultas' => []]));
        }
        $content = file_get_contents(DB_FILE);
        return json_decode($content, true) ?: ['consultas' => []];
    }
}

if (!function_exists('saveDbData')) {
    function saveDbData($data) {
        file_put_contents(DB_FILE, json_encode($data, JSON_PRETTY_PRINT));
    }
}

// --- CONTROLLERS (Lógica Backend) ---

// 1. LOGIN (Autenticação via JSON)
if (isset($_GET['action']) && $_GET['action'] == 'auth') {
    $usuarioInput = $_POST['usuario'] ?? ''; // CPF ou CRM
    $senhaInput = $_POST['senha'] ?? '';
    $tipoUsuario = $_POST['tipo_usuario'] ?? 'paciente';

    // LOGIN DE ADMIN (Hardcoded para segurança do MVP)
    if ($usuarioInput === 'admin' && $senhaInput === 'admin123') {
        $_SESSION['user_id'] = 0;
        $_SESSION['nome'] = 'Administrador';
        $_SESSION['tipo_usuario'] = 'admin';
        header('Location: index.php?page=home');
        exit;
    }

    $usuarioEncontrado = null;

    if ($tipoUsuario == 'paciente') {
        // Carrega Pacientes do JSON
        $data = getJsonData('pacientes.json');
        $listaPacientes = $data['pacientes'] ?? [];

        // Busca Paciente pelo CPF
        foreach ($listaPacientes as $p) {
            // Remove pontuação para comparar apenas números (boa prática)
            $cpfLimpoBanco = preg_replace('/[^0-9]/', '', $p['cpf']);
            $cpfLimpoInput = preg_replace('/[^0-9]/', '', $usuarioInput);

            if ($cpfLimpoBanco === $cpfLimpoInput) {
                // Verifica a senha (em texto plano para este MVP)
                if (isset($p['senha']) && $p['senha'] === $senhaInput) {
                    $usuarioEncontrado = $p;
                    break;
                }
            }
        }

    } else { // Médico
        // Carrega Médicos do JSON
        $data = getJsonData('medicos.json');
        $listaMedicos = $data['medicos'] ?? [];

        // Busca Médico pelo CPF ou CRM
        foreach ($listaMedicos as $m) {
            // Verifica se o input bate com CPF (limpo) OU com CRM (exato)
            $cpfLimpoBanco = preg_replace('/[^0-9]/', '', $m['cpf'] ?? '');
            $cpfLimpoInput = preg_replace('/[^0-9]/', '', $usuarioInput);
            
            // Comparação flexível: aceita CPF (só números) ou CRM (texto exato)
            $matchCpf = ($cpfLimpoBanco !== '' && $cpfLimpoBanco === $cpfLimpoInput);
            $matchCrm = (isset($m['crm']) && strtoupper($m['crm']) === strtoupper($usuarioInput));

            if ($matchCpf || $matchCrm) {
                if (isset($m['senha']) && $m['senha'] === $senhaInput) {
                    $usuarioEncontrado = $m;
                    break;
                }
            }
        }
    }

    // Resultado da Autenticação
    if ($usuarioEncontrado) {
        // Login Sucesso: Salva na Sessão
        $_SESSION['user_id'] = $usuarioEncontrado['id'];
        $_SESSION['nome'] = $usuarioEncontrado['nome'];
        $_SESSION['tipo_usuario'] = $tipoUsuario;
        
        // Se for médico, salva a especialidade na sessão para filtrar depois
        // Importante: garantir que as especialidades sejam salvas para o filtro do dashboard
        if ($tipoUsuario == 'medico') {
            $_SESSION['especialidades'] = $usuarioEncontrado['especialidades'] ?? [];
        }
        
        header('Location: index.php?page=home');
        exit;
    } else {
        // Login Falha
        header('Location: index.php?page=login&error=1');
        exit;
    }
}

// 2. LOGOUT
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header('Location: index.php?page=login');
    exit;
}

// 3. AGENDAR CONSULTA (Lógica Inteligente de Atribuição)
if (isset($_GET['action']) && $_GET['action'] == 'agendar') {
    $db = getDbData();
    $especialidadeDesejada = $_POST['especialidade'];
    
    // Carrega a lista de médicos para encontrar o especialista
    $dataMedicos = getJsonData('medicos.json');
    $listaMedicos = $dataMedicos['medicos'] ?? [];
    
    $medicoSelecionado = 'Médico de Plantão'; // Fallback
    
    // Procura o primeiro médico que atende a especialidade
    foreach ($listaMedicos as $m) {
        if (in_array($especialidadeDesejada, $m['especialidades'])) {
            $medicoSelecionado = $m['nome'];
            break; // Encontrou um médico, para a busca
        }
    }
    
    $novaConsulta = [
        'id' => uniqid(),
        'paciente_nome' => $_SESSION['nome'], // Nome da sessão (quem está logado)
        'medico_nome' => $medicoSelecionado, // Médico correto baseado na especialidade
        'especialidade' => $especialidadeDesejada,
        'data' => $_POST['data'],
        'hora' => $_POST['hora'],
        'status' => 'pendente'
    ];
    
    // Garante que o array existe
    if (!isset($db['consultas'])) {
        $db['consultas'] = [];
    }
    
    $db['consultas'][] = $novaConsulta;
    saveDbData($db);
    header('Location: index.php?page=home&msg=sucesso');
    exit;
}

// 4. CADASTRO DE MÉDICO (ADMIN)
if (isset($_GET['action']) && $_GET['action'] == 'cadastrar_medico') {
    if ($_SESSION['tipo_usuario'] !== 'admin') die('Acesso negado');

    $data = getJsonData('medicos.json');
    if (!isset($data['medicos'])) $data['medicos'] = [];

    $novoMedico = [
        'id' => count($data['medicos']) + 100, // ID simples
        'nome' => $_POST['nome'],
        'cpf' => $_POST['cpf'],
        'crm' => $_POST['crm'],
        'senha' => $_POST['senha'], // Texto plano para MVP
        'especialidades' => [$_POST['especialidade']], // Simplificado para 1
        'email' => $_POST['email'],
        'telefone' => $_POST['telefone']
    ];

    $data['medicos'][] = $novoMedico;
    saveJsonData('medicos.json', $data);
    header('Location: index.php?page=home&msg=medico_cadastrado');
    exit;
}

// 5. CADASTRO DE PACIENTE (ADMIN)
if (isset($_GET['action']) && $_GET['action'] == 'cadastrar_paciente') {
    if ($_SESSION['tipo_usuario'] !== 'admin') die('Acesso negado');

    $data = getJsonData('pacientes.json');
    if (!isset($data['pacientes'])) $data['pacientes'] = [];

    $novoPaciente = [
        'id' => count($data['pacientes']) + 1,
        'nome' => $_POST['nome'],
        'cpf' => $_POST['cpf'],
        'senha' => $_POST['senha'],
        'email' => $_POST['email'],
        'telefone' => $_POST['telefone'],
        'prontuario' => ['numero' => date('Y') . (count($data['pacientes']) + 1)]
    ];

    $data['pacientes'][] = $novoPaciente;
    saveJsonData('pacientes.json', $data);
    header('Location: index.php?page=home&msg=paciente_cadastrado');
    exit;
}

// 6. PRESCREVER (Simulação)
if (isset($_GET['action']) && $_GET['action'] == 'prescrever') {
    header('Location: index.php?page=home&msg=prescricao_enviada');
    exit;
}

// 7. CANCELAR CONSULTA
if (isset($_GET['action']) && $_GET['action'] == 'cancelar') {
    $id = $_POST['id'];
    $db = getDbData();
    if (isset($db['consultas'])) {
        foreach ($db['consultas'] as $key => $consulta) {
            if ($consulta['id'] == $id) {
                // Segurança: Só pode cancelar se for o dono da consulta (ou médico)
                // Aqui simplificamos permitindo se o ID bater, já que a lista é filtrada na visualização
                $db['consultas'][$key]['status'] = 'cancelada';
                break;
            }
        }
        saveDbData($db);
    }
    header('Location: index.php?page=home&msg=cancelado');
    exit;
}

// 8. REMARCAR CONSULTA
if (isset($_GET['action']) && $_GET['action'] == 'remarcar') {
    $id = $_POST['id_remarcar'];
    $db = getDbData();
    if (isset($db['consultas'])) {
        foreach ($db['consultas'] as $key => $consulta) {
            if ($consulta['id'] == $id) {
                $db['consultas'][$key]['data'] = $_POST['data'];
                $db['consultas'][$key]['hora'] = $_POST['hora'];
                $db['consultas'][$key]['status'] = 'pendente';
                break;
            }
        }
        saveDbData($db);
    }
    header('Location: index.php?page=home&msg=remarcado');
    exit;
}

// 9. FINALIZAR CONSULTA E MARCAR RETORNO (NOVO)
if (isset($_GET['action']) && $_GET['action'] == 'finalizar_consulta') {
    $id = $_POST['id_consulta'];
    $marcarRetorno = isset($_POST['marcar_retorno']);
    
    $db = getDbData();
    
    if (isset($db['consultas'])) {
        // 1. Atualiza o status da consulta atual para 'finalizada'
        foreach ($db['consultas'] as $key => $consulta) {
            if ($consulta['id'] == $id) {
                $db['consultas'][$key]['status'] = 'finalizada';
                
                // Dados para o retorno (se houver)
                $pacienteNome = $consulta['paciente_nome'];
                $medicoNome = $consulta['medico_nome'];
                $especialidade = $consulta['especialidade'];
                break;
            }
        }
        
        // 2. Se pediu retorno, cria nova consulta
        if ($marcarRetorno && isset($pacienteNome)) {
            $novaData = $_POST['data_retorno'];
            $novoHorario = $_POST['hora_retorno'];
            
            $novaConsulta = [
                'id' => uniqid(),
                'paciente_nome' => $pacienteNome,
                'medico_nome' => $medicoNome,
                'especialidade' => $especialidade,
                'data' => $novaData,
                'hora' => $novoHorario,
                'status' => 'pendente',
                'tipo' => 'retorno' // Opcional: flag para identificar retorno
            ];
            $db['consultas'][] = $novaConsulta;
        }
        
        saveDbData($db);
    }
    
    header('Location: index.php?page=home&msg=consulta_finalizada');
    exit;
}

// 10. EXCLUIR CONSULTA (Para limpar histórico)
if (isset($_GET['action']) && $_GET['action'] == 'excluir_consulta') {
    $id = $_POST['id'];
    $db = getDbData();
    
    if (isset($db['consultas'])) {
        foreach ($db['consultas'] as $key => $consulta) {
            if ($consulta['id'] == $id) {
                // TRAVA DE SEGURANÇA:
                // Só permite excluir se estiver 'finalizada' (ou 'cancelada')
                // Isso impede que o paciente exclua uma consulta 'pendente' sem querer
                if ($consulta['status'] === 'finalizada' || $consulta['status'] === 'cancelada') {
                    unset($db['consultas'][$key]);
                    
                    // Reorganiza os índices do array (boa prática para JSON)
                    $db['consultas'] = array_values($db['consultas']);
                }
                break;
            }
        }
        saveDbData($db);
    }
    
    header('Location: index.php?page=home&msg=registro_removido');
    exit;
}

// --- ROTEADOR DE VIEWS ---
$page = isset($_GET['page']) ? $_GET['page'] : 'login';

// Middleware de Autenticação
if ($page != 'login' && !isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
if ($page == 'login' && isset($_SESSION['user_id'])) {
    header('Location: index.php?page=home');
    exit;
}

// Carregamento de Dados para a Home (COM FILTRAGEM)
if ($page == 'home') {
    $db = getDbData();
    $todasConsultas = $db['consultas'] ?? [];
    $consultas = []; // Array que será passado para a view

    if ($_SESSION['tipo_usuario'] == 'paciente') {
        // PACIENTE: Vê apenas as SUAS consultas (filtrar por nome)
        $consultas = array_filter($todasConsultas, function($c) {
            return $c['paciente_nome'] === $_SESSION['nome'];
        });
    } elseif ($_SESSION['tipo_usuario'] == 'medico') {
        // MÉDICO: Vê apenas consultas vinculadas ao SEU NOME
        // Isso garante que o cardiologista não veja a consulta do ortopedista
        $nomeMedicoLogado = $_SESSION['nome'];
        
        $consultas = array_filter($todasConsultas, function($c) use ($nomeMedicoLogado) {
            // Verifica se o nome do médico na consulta é igual ao nome do médico logado
            return $c['medico_nome'] === $nomeMedicoLogado;
        });
    } else {
        // ADMIN: Vê TUDO + Dados Financeiros
        $consultas = $todasConsultas;
        
        // Dados Extras para Admin
        $dadosAdmin = [];
        // Financeiro: R$ 150 por consulta não cancelada
        $consultasRealizadas = array_filter($todasConsultas, fn($c) => $c['status'] !== 'cancelada');
        $dadosAdmin['faturamento'] = count($consultasRealizadas) * 150;
        
        // Consultas Hoje
        $hoje = date('Y-m-d');
        $consultasHoje = array_filter($todasConsultas, fn($c) => $c['data'] === $hoje && $c['status'] !== 'cancelada');
        $dadosAdmin['consultas_hoje'] = count($consultasHoje);
        
        // Total de Usuários
        $pacientes = getJsonData('pacientes.json');
        $medicos = getJsonData('medicos.json');
        $dadosAdmin['total_pacientes'] = count($pacientes['pacientes'] ?? []);
        $dadosAdmin['total_medicos'] = count($medicos['medicos'] ?? []);
        
        // Disponibiliza para a view
        $GLOBALS['dadosAdmin'] = $dadosAdmin; 
    }
}

// Inclusão da View
$filename = VIEW_PATH . $page . '.php';
if (file_exists($filename)) {
    include $filename;
} else {
    // Fallback simples para erro 404
    echo "<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>";
    echo "<h1>Erro 404</h1>";
    echo "<p>A página solicitada não foi encontrada.</p>";
    echo "<a href='index.php'>Voltar ao início</a>";
    echo "</div>";
}