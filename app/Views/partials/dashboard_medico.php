<div id="view-medico" class="flex-1 overflow-y-auto p-8 fade-in">
    
    <!-- CABEÇALHO (Mantido) -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div class="flex items-center gap-4">
            <!-- Corrigido: Logotipo apenas na sidebar, aqui fica vazio ou ícone de médico se quiser -->
            <!-- Mantendo o layout anterior sem logo extra aqui conforme pedido -->
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Painel Médico</h1>
                <p class="text-slate-500 mt-1">Bem-vindo, <?= $_SESSION['nome'] ?>.</p>
            </div>
        </div>
        
        <div class="flex items-center gap-2 bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold border border-green-200">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </span>
            Sistema Online
        </div>
    </div>

    <!-- ESTATÍSTICAS (PHP Mantido) -->
    <?php
        $ativas = array_filter($consultas, fn($c) => $c['status'] !== 'cancelada');
        $totalHoje = count($ativas);
        $totalEspera = count(array_filter($ativas, fn($c) => in_array($c['status'], ['pendente', 'confirmada'])));
        $totalFinalizados = count(array_filter($consultas, fn($c) => $c['status'] === 'finalizada'));
    ?>

    <!-- ESTATÍSTICAS (Visual Mantido) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card-modern flex flex-col items-center justify-center p-6 text-center">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Hoje</span>
            <span class="text-4xl font-extrabold text-slate-800"><?= $totalHoje ?></span>
            <span class="text-sm text-slate-500 mt-1">Pacientes Agendados</span>
        </div>
        <div class="card-modern flex flex-col items-center justify-center p-6 text-center border-b-4 border-orange-400">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Espera</span>
            <span class="text-4xl font-extrabold text-orange-500"><?= $totalEspera ?></span>
            <span class="text-sm text-slate-500 mt-1">Na sala virtual</span>
        </div>
        <div class="card-modern flex flex-col items-center justify-center p-6 text-center border-b-4 border-green-500">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Finalizados</span>
            <span class="text-4xl font-extrabold text-green-500"><?= $totalFinalizados ?></span>
            <span class="text-sm text-slate-500 mt-1">Atendimentos</span>
        </div>
    </div>

    <!-- AGENDA DO DIA -->
    <div class="card-modern p-0 overflow-hidden" style="min-height: 400px;">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white">
            <h3 class="text-lg font-bold text-slate-800">Agenda de Hoje</h3>
            <span class="text-sm text-slate-500"><?= date('d \d\e F, Y') ?></span>
        </div>

        <div class="divide-y divide-slate-100">
            <?php if(empty($consultas)): ?>
                <div class="p-12 text-center">
                    <div class="bg-slate-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="calendar-off" class="text-slate-400 w-8 h-8"></i>
                    </div>
                    <h3 class="text-slate-800 font-bold">Agenda Livre</h3>
                    <p class="text-slate-500 text-sm mt-1">Nenhum paciente agendado para hoje.</p>
                </div>
            <?php else: ?>
                <?php foreach($consultas as $index => $c): ?>
                    <?php 
                        date_default_timezone_set('America/Sao_Paulo');
                        $agora = time();
                        $consultaTimestamp = strtotime($c['data'] . ' ' . $c['hora']);
                        $diferencaMinutos = ($consultaTimestamp - $agora) / 60;

                        $statusBadge = '';
                        $statusText = '';
                        $isAtendimento = false;
                        
                        if ($c['status'] === 'cancelada') {
                            $statusBadge = '<span class="bg-slate-100 text-slate-500 text-[10px] font-bold px-2 py-0.5 rounded mt-1 border border-slate-200">CANCELADO</span>';
                        } elseif ($c['status'] === 'finalizada') {
                            $statusBadge = '<span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded mt-1 border border-blue-200">FINALIZADO</span>';
                        } else {
                            if ($diferencaMinutos <= 10 && $diferencaMinutos >= -60) { 
                                $statusBadge = '<span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded mt-1 border border-green-200 animate-pulse">AGORA</span>';
                                $isAtendimento = true;
                            } elseif ($diferencaMinutos < -60) {
                                $statusBadge = '<span class="bg-red-100 text-red-700 text-[10px] font-bold px-2 py-0.5 rounded mt-1 border border-red-200">EM ATRASO</span>';
                                $isAtendimento = true;
                            } else {
                                $horas = floor($diferencaMinutos / 60);
                                $mins = $diferencaMinutos % 60;
                                $statusText = $horas > 0 ? "Em {$horas}h {$mins}m" : "Em " . round($diferencaMinutos) . " min";
                                $isAtendimento = false;
                            }
                        }
                    ?>

                    <div class="p-5 flex flex-col md:flex-row items-center gap-6 hover:bg-slate-50 transition-colors group relative <?= in_array($c['status'], ['cancelada', 'finalizada']) ? 'opacity-60 bg-slate-50' : '' ?>">
                        
                        <!-- Coluna Horário -->
                        <div class="flex flex-col items-center min-w-[80px]">
                            <span class="text-xl font-bold text-slate-800"><?= $c['hora'] ?></span>
                            <?= $statusBadge ?>
                            <?php if($statusText): ?>
                                <span class="text-xs text-slate-400 mt-1"><?= $statusText ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Coluna Informações -->
                        <div class="flex-1 text-center md:text-left w-full">
                            <div class="flex items-center justify-center md:justify-start gap-3 mb-1">
                                <h4 class="text-lg font-bold text-slate-800 <?= in_array($c['status'], ['cancelada', 'finalizada']) ? 'line-through decoration-slate-400' : '' ?>">
                                    <?= $c['paciente_nome'] ?>
                                </h4>
                                <?php if($c['especialidade'] == 'Cardiologia'): ?>
                                    <span class="badge-telemedicina">Telemedicina</span>
                                <?php else: ?>
                                    <span class="bg-blue-50 text-blue-700 text-xs font-bold px-2 py-1 rounded-full">Presencial</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-sm text-slate-500 flex items-center justify-center md:justify-start gap-2">
                                <i data-lucide="activity" class="w-4 h-4 text-blue-400"></i>
                                <?= $c['especialidade'] ?> 
                                <span class="text-slate-300">•</span> 
                                <?= isset($c['tipo']) && $c['tipo'] == 'retorno' ? 'Retorno' : 'Consulta' ?>
                            </p>
                        </div>

                        <!-- Coluna Ações -->
                        <div class="flex items-center gap-3 w-full md:w-auto mt-4 md:mt-0 justify-end">
                            
                            <!-- Botões de Ação para Consultas Pendentes/Ativas -->
                            <?php if($c['status'] !== 'cancelada' && $c['status'] !== 'finalizada'): ?>
                                
                                <!-- Botão Prontuário (CORRIGIDO: Chama abrirProntuario) -->
                                <button onclick="abrirProntuario('<?= $c['paciente_nome'] ?>')" class="border border-slate-300 text-slate-600 bg-white hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm hidden sm:block">
                                    Prontuário
                                </button>

                                <?php if($isAtendimento): ?>
                                    <!-- Botão Atender -->
                                    <button onclick="iniciarAtendimento()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-md flex items-center justify-center gap-2">
                                        <i data-lucide="video" class="w-4 h-4"></i> Atender
                                    </button>
                                    
                                    <!-- Botão Finalizar -->
                                    <button onclick="abrirModalFinalizar('<?= $c['id'] ?>')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-md flex items-center justify-center gap-2">
                                        <i data-lucide="check-square" class="w-4 h-4"></i> Finalizar
                                    </button>
                                <?php else: ?>
                                    <button class="bg-slate-100 text-slate-400 px-5 py-2 rounded-lg text-sm font-semibold cursor-not-allowed flex items-center justify-center gap-2 border border-slate-200">
                                        <i data-lucide="clock" class="w-4 h-4"></i> Aguarde
                                    </button>
                                <?php endif; ?>

                                <!-- Menu Kebab -->
                                <div class="relative">
                                    <button onclick="toggleMenu('menu-<?= $index ?>')" class="p-2 hover:bg-slate-200 rounded-full text-slate-500 transition focus:outline-none">
                                        <i data-lucide="more-vertical" class="w-5 h-5"></i>
                                    </button>
                                    <div id="menu-<?= $index ?>" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-slate-100 z-50 text-left overflow-hidden">
                                        <button onclick="openModal('modal-prescricao'); toggleMenu('menu-<?= $index ?>')" class="w-full text-left px-4 py-3 hover:bg-slate-50 text-slate-700 text-sm flex items-center gap-2 border-b border-slate-50">
                                            <i data-lucide="file-plus" class="w-4 h-4 text-blue-500"></i> Prescrever
                                        </button>
                                        
                                        <!-- Opção de Finalizar também no menu -->
                                        <button onclick="abrirModalFinalizar('<?= $c['id'] ?>'); toggleMenu('menu-<?= $index ?>')" class="w-full text-left px-4 py-3 hover:bg-green-50 text-green-700 text-sm flex items-center gap-2 border-b border-slate-50">
                                            <i data-lucide="check-circle" class="w-4 h-4"></i> Finalizar & Retorno
                                        </button>
                                        
                                        <!-- Botão Prontuário no Menu (CORRIGIDO: Chama abrirProntuario) -->
                                        <button onclick="abrirProntuario('<?= $c['paciente_nome'] ?>'); toggleMenu('menu-<?= $index ?>')" class="w-full text-left px-4 py-3 hover:bg-slate-50 text-slate-700 text-sm flex items-center gap-2 sm:hidden">
                                            <i data-lucide="file-text" class="w-4 h-4 text-slate-400"></i> Prontuário
                                        </button>
                                        
                                        <button onclick="alert('Ação restrita à secretaria.'); toggleMenu('menu-<?= $index ?>')" class="w-full text-left px-4 py-3 hover:bg-red-50 text-red-600 text-sm flex items-center gap-2">
                                            <i data-lucide="x-circle" class="w-4 h-4"></i> Cancelar
                                        </button>
                                    </div>
                                </div>

                            <?php else: ?>
                                <!-- Se finalizado ou cancelado, mostra apenas o status visualmente -->
                                <span class="text-xs text-slate-400 font-medium italic px-3">Arquivado</span>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function iniciarAtendimento() {
        window.open("https://meet.google.com/new", '_blank');
    }

    function toggleMenu(menuId) {
        document.querySelectorAll('[id^="menu-"]').forEach(menu => {
            if (menu.id !== menuId) menu.classList.add('hidden');
        });
        const menu = document.getElementById(menuId);
        if (menu) menu.classList.toggle('hidden');
    }

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.relative')) {
            document.querySelectorAll('[id^="menu-"]').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });
</script>

