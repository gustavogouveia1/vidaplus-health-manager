<div id="view-pacientes" class="flex-1 overflow-y-auto p-8 fade-in">
    
    <!-- CABEÇALHO -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Meus Pacientes</h1>
            <p class="text-slate-500 mt-1">Gerencie seus pacientes e acesse prontuários.</p>
        </div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md transition flex items-center gap-2">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Novo Paciente
        </button>
    </div>

    <!-- FILTROS -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm mb-6 flex gap-4 items-center">
        <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-3 top-3 text-slate-400 w-5 h-5"></i>
            <input type="text" placeholder="Buscar por nome, CPF ou prontuário..." class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <select class="border border-slate-300 rounded-lg px-4 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none text-slate-600">
            <option>Todos os Status</option>
            <option>Ativos</option>
            <option>Inativos</option>
        </select>
    </div>

    <!-- LISTA DE PACIENTES -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <?php 
            // LÓGICA DE CARREGAMENTO (Idealmente estaria no Controller, mas para MVP fica aqui)
            
            // 1. Carrega dados
            $dadosMedicos = getJsonData('medicos.json');
            $dadosPacientes = getJsonData('pacientes.json');
            
            // 2. Encontra o médico logado
            $medicoLogado = null;
            foreach ($dadosMedicos['medicos'] ?? [] as $m) {
                if ($m['id'] == $_SESSION['user_id']) { // Usando ID da sessão (99, 100, etc)
                    $medicoLogado = $m;
                    break;
                }
            }

            // 3. Filtra pacientes vinculados
            $idsVinculados = $medicoLogado['pacientes_vinculados'] ?? [];
            $meusPacientes = [];
            
            if (!empty($idsVinculados)) {
                foreach ($dadosPacientes['pacientes'] ?? [] as $p) {
                    if (in_array($p['id'], $idsVinculados)) {
                        $meusPacientes[] = $p;
                    }
                }
            }
        ?>

        <?php if (empty($meusPacientes)): ?>
            <div class="col-span-full text-center py-12">
                <div class="bg-slate-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                    <i data-lucide="users" class="w-8 h-8"></i>
                </div>
                <h3 class="text-slate-600 font-bold">Nenhum paciente vinculado</h3>
                <p class="text-slate-400 text-sm">Você ainda não possui pacientes na sua lista.</p>
            </div>
        <?php else: ?>
            <?php foreach($meusPacientes as $p): ?>
                <!-- Card do Paciente -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition group overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($p['nome']) ?>&background=random&color=fff" class="w-12 h-12 rounded-full border-2 border-white shadow-sm">
                                <div>
                                    <h3 class="font-bold text-slate-800 text-lg group-hover:text-blue-600 transition"><?= $p['nome'] ?></h3>
                                    <p class="text-xs text-slate-500">Prontuário: #<?= $p['prontuario']['numero'] ?? '---' ?></p>
                                </div>
                            </div>
                            <button class="text-slate-400 hover:text-blue-600"><i data-lucide="more-horizontal"></i></button>
                        </div>
                        
                        <div class="space-y-2 text-sm text-slate-600 mb-6">
                            <div class="flex items-center gap-2">
                                <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                <span>Nasc: <?= date('d/m/Y', strtotime($p['data_nascimento'])) ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i data-lucide="phone" class="w-4 h-4 text-slate-400"></i>
                                <span><?= $p['telefone'] ?></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i data-lucide="map-pin" class="w-4 h-4 text-slate-400"></i>
                                <span class="truncate"><?= $p['endereco']['cidade'] ?>/<?= $p['endereco']['estado'] ?></span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button onclick="abrirProntuario('<?= $p['nome'] ?>')" class="flex-1 bg-blue-50 text-blue-700 py-2 rounded-lg text-sm font-bold hover:bg-blue-100 transition flex items-center justify-center gap-2">
                                <i data-lucide="file-text" class="w-4 h-4"></i> Prontuário
                            </button>
                            <button class="px-3 py-2 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 transition" title="Enviar Mensagem">
                                <i data-lucide="message-circle" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                    <?php if(!empty($p['prontuario']['alergias'])): ?>
                        <div class="bg-red-50 px-6 py-2 text-xs font-bold text-red-600 border-t border-red-100 flex items-center gap-2">
                            <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                            Alergia: <?= implode(', ', $p['prontuario']['alergias']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>