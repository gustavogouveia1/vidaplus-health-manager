<?php
// --- LÓGICA DE PROCESSAMENTO DE DADOS (DASHBOARD) ---
// Vamos calcular tudo aqui no topo para deixar o HTML limpo

$faturamentoRealizado = 0;
$faturamentoEstimado = 0;
$dadosGrafico = []; // Array para agrupar valores por data: ['2023-10-01' => 450.00]

foreach ($consultas as $c) {
    // Ignora canceladas para contas financeiras
    if ($c['status'] == 'cancelada') continue;

    $valorConsulta = 150.00; // Valor fixo conforme regra de negócio

    // 1. Faturamento Estimado (Tudo que não foi cancelado: Pendente + Confirmada + Finalizada)
    $faturamentoEstimado += $valorConsulta;

    // 2. Faturamento Realizado (Apenas dinheiro em caixa: Finalizada)
    if ($c['status'] == 'finalizada') {
        $faturamentoRealizado += $valorConsulta;
    }

    // 3. Preparação para o Gráfico (Agrupar por data)
    // Se a data já existe no array, soma. Se não, cria.
    $dataDb = $c['data']; // Y-m-d
    if (!isset($dadosGrafico[$dataDb])) {
        $dadosGrafico[$dataDb] = 0;
    }
    $dadosGrafico[$dataDb] += $valorConsulta;
}

// Ordena o gráfico por data (Crescente) para a linha do tempo fazer sentido
ksort($dadosGrafico);

// Prepara arrays para o JavaScript ler
$labelsGrafico = json_encode(array_map(function($date){ return date('d/m', strtotime($date)); }, array_keys($dadosGrafico)));
$valoresGrafico = json_encode(array_values($dadosGrafico));
?>

<div id="view-admin" class="flex-1 overflow-y-auto p-8 fade-in">
    
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Painel Administrativo</h1>
            <p class="text-slate-500 mt-1">Gestão financeira e performance clínica.</p>
        </div>
        <div class="mt-4 md:mt-0 text-right">
            <span class="text-xs text-slate-400 uppercase font-bold">Data do Sistema</span>
            <p class="text-slate-800 font-medium"><?= date('d \d\e F, Y') ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        
        <div class="card-modern flex flex-col p-6 bg-white border-l-4 border-green-600 shadow-md">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-bold text-green-600 uppercase tracking-wider mb-2 block">Receita Consolidada</span>
                    <span class="text-3xl font-extrabold text-slate-800">
                        R$ <?= number_format($faturamentoRealizado, 2, ',', '.') ?>
                    </span>
                </div>
                <div class="bg-green-100 p-2 rounded-lg text-green-600">
                    <i data-lucide="wallet" class="w-6 h-6"></i>
                </div>
            </div>
            <span class="text-xs text-slate-400 mt-2">Apenas consultas finalizadas</span>
        </div>

        <div class="card-modern flex flex-col p-6 bg-white border-l-4 border-yellow-500 shadow-sm opacity-90">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-bold text-yellow-600 uppercase tracking-wider mb-2 block">Previsão de Receita</span>
                    <span class="text-3xl font-extrabold text-slate-700">
                        R$ <?= number_format($faturamentoEstimado, 2, ',', '.') ?>
                    </span>
                </div>
                <div class="bg-yellow-100 p-2 rounded-lg text-yellow-600">
                    <i data-lucide="trending-up" class="w-6 h-6"></i>
                </div>
            </div>
            <span class="text-xs text-slate-400 mt-2">Inclui pendentes e agendados</span>
        </div>

        <div class="card-modern flex flex-col p-6 bg-white border-l-4 border-blue-500">
             <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-bold text-blue-500 uppercase tracking-wider mb-2 block">Agenda do Dia</span>
                    <span class="text-3xl font-extrabold text-slate-800">
                        <?= $GLOBALS['dadosAdmin']['consultas_hoje'] ?>
                    </span>
                </div>
                 <div class="bg-blue-100 p-2 rounded-lg text-blue-600">
                    <i data-lucide="calendar" class="w-6 h-6"></i>
                </div>
            </div>
            <span class="text-xs text-slate-400 mt-2">Pacientes para hoje</span>
        </div>

        <div class="card-modern flex flex-col p-6 bg-white border-l-4 border-slate-400">
             <div class="flex justify-between items-start">
                <div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Base de Usuários</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-extrabold text-slate-800"><?= $GLOBALS['dadosAdmin']['total_pacientes'] ?></span>
                        <span class="text-sm text-slate-400">Paci.</span>
                        <span class="text-slate-300 mx-1">|</span>
                        <span class="text-xl font-bold text-slate-600"><?= $GLOBALS['dadosAdmin']['total_medicos'] ?></span>
                        <span class="text-sm text-slate-400">Méd.</span>
                    </div>
                </div>
                 <div class="bg-slate-100 p-2 rounded-lg text-slate-600">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
            </div>
            <span class="text-xs text-slate-400 mt-2">Total cadastrado no sistema</span>
        </div>
    </div>

    <div class="card-modern p-6 mb-8 bg-white relative">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <i data-lucide="bar-chart-2" class="w-5 h-5 text-blue-600"></i>
            Fluxo de Faturamento (Linha do Tempo)
        </h3>
        <div class="w-full h-[300px]">
            <canvas id="financeChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <button onclick="openModal('modal-cadastro-medico')" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition flex items-center gap-4 group">
            <div class="bg-blue-50 p-3 rounded-full text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition">
                <i data-lucide="stethoscope" class="w-5 h-5"></i>
            </div>
            <div class="text-left">
                <h3 class="font-bold text-slate-800 text-sm">Novo Médico</h3>
                <p class="text-xs text-slate-500">Adicionar especialista</p>
            </div>
        </button>

        <button onclick="openModal('modal-cadastro-paciente')" class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition flex items-center gap-4 group">
            <div class="bg-green-50 p-3 rounded-full text-green-600 group-hover:bg-green-600 group-hover:text-white transition">
                <i data-lucide="user-plus" class="w-5 h-5"></i>
            </div>
            <div class="text-left">
                <h3 class="font-bold text-slate-800 text-sm">Novo Paciente</h3>
                <p class="text-xs text-slate-500">Registrar usuário</p>
            </div>
        </button>
    </div>

    <div class="card-modern p-0 overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-white flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800">Relatório Detalhado</h3>
            <span class="text-xs bg-slate-100 px-2 py-1 rounded text-slate-500">Últimos Agendamentos</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full table-modern text-left">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Status</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Ordenar tabela pela data mais recente primeiro
                    $consultasInvertidas = array_reverse($consultas);
                    foreach($consultasInvertidas as $c): 
                    ?>
                    <tr class="table-row-modern border-b border-slate-50">
                        <td class="font-medium text-slate-600">
                            <?= date('d/m/Y', strtotime($c['data'])) ?> <span class="text-xs text-slate-400"><?= $c['hora'] ?></span>
                        </td>
                        <td><?= $c['paciente_nome'] ?></td>
                        <td><?= $c['medico_nome'] ?></td>
                        <td>
                            <?php 
                                $corStatus = match($c['status']) { 
                                    'cancelada' => 'bg-red-100 text-red-700', 
                                    'finalizada' => 'bg-blue-100 text-blue-700',
                                    'confirmada' => 'bg-green-100 text-green-700', 
                                    default => 'bg-yellow-100 text-yellow-700' 
                                };
                            ?>
                            <span class="<?= $corStatus ?> px-2 py-1 rounded text-xs font-bold uppercase"><?= $c['status'] ?></span>
                        </td>
                        <td class="font-bold text-slate-700">
                            <?= $c['status'] == 'cancelada' ? '<span class="text-slate-300 line-through text-xs">R$ 0,00</span>' : '<span class="text-green-600">R$ 150,00</span>' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Configuração do Gráfico Financeiro
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('financeChart').getContext('2d');
        
        // Dados vindos do PHP
        const labels = <?= $labelsGrafico ?>;
        const dataValues = <?= $valoresGrafico ?>;

        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)'); // Azul transparente topo
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');   // Transparente base

        new Chart(ctx, {
            type: 'line', // Tipo Linha para mostrar evolução
            data: {
                labels: labels,
                datasets: [{
                    label: 'Faturamento Diário (R$)',
                    data: dataValues,
                    borderColor: '#2563eb', // Cor da linha (Azul Tailwind)
                    backgroundColor: gradient, // Fundo gradiente
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#2563eb',
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true, // Preencher área abaixo da linha
                    tension: 0.4 // Curvatura da linha (suave)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // Esconde legenda pois só tem 1 dado
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [5, 5],
                            color: '#f1f5f9'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value;
                            },
                            color: '#64748b',
                            font: {
                                size: 11
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    });
</script>