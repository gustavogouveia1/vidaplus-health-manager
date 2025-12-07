<div id="view-paciente" class="flex-1 overflow-y-auto p-8 fade-in">
    
    <!-- CABEÇALHO DA PÁGINA -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Olá, <?= explode(' ', $_SESSION['nome'])[0] ?></h1>
            
            <p class="text-slate-500 mt-1">
                Sua saúde está em dia. Você tem 
                <span class="font-bold text-blue-600">
                    <?= count(array_filter($consultas, fn($c) => $c['status'] === 'pendente' || $c['status'] === 'confirmada')) ?>
                </span> 
                consulta(s) pendente(s).
            </p>
        </div>
        
        <button onclick="openModal('modal-agendar')" class="btn-primary-modern flex items-center gap-2 mt-4 md:mt-0">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Agendar Consulta
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- COLUNA PRINCIPAL (Esquerda) -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- CARD PRÓXIMA CONSULTA (Destaque) -->
        <?php 
            // Pega a próxima consulta não cancelada nem finalizada
            $proxima = null;
            foreach($consultas as $c) {
                if($c['status'] != 'cancelada' && $c['status'] != 'finalizada') {
                    $proxima = $c;
                    break; // Pega a primeira que achar
                }
            }
        ?>

            <?php if($proxima): ?>
            <div class="card-modern relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-blue-600"></div> <!-- Faixa lateral azul -->
                
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="calendar" class="text-blue-600 w-5 h-5"></i>
                        Próxima Consulta
                    </h3>
                    <span class="badge-telemedicina">Telemedicina</span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg">
                        <?= substr($proxima['medico_nome'], 4, 1) ?>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-slate-800"><?= $proxima['medico_nome'] ?></h4>
                        <p class="text-sm text-slate-500"><?= $proxima['especialidade'] ?> • <?= date('d/m', strtotime($proxima['data'])) ?> às <?= $proxima['hora'] ?></p>
                    </div>
                    
                    <!-- Botão de Ação Condicional -->
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition flex items-center gap-2">
                        <i data-lucide="video" class="w-4 h-4"></i> Entrar na Sala
                    </button>
                </div>
                
                <p class="mt-6 text-xs text-slate-400 flex items-center gap-1">
                    <i data-lucide="shield-check" class="w-3 h-3"></i>
                    Ambiente seguro e criptografado conforme LGPD.
                </p>
            </div>
            <?php else: ?>
                <!-- Estado vazio elegante -->
                <div class="card-modern p-8 text-center">
                    <div class="bg-slate-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="calendar-check" class="text-slate-400 w-8 h-8"></i>
                    </div>
                    <h3 class="text-slate-800 font-bold">Nenhuma consulta agendada</h3>
                    <p class="text-slate-500 text-sm mt-1">Utilize o botão acima para marcar seu primeiro atendimento.</p>
                </div>
            <?php endif; ?>

            <!-- HISTÓRICO RECENTE (Tabela Limpa) -->
            <div class="card-modern p-0 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">Histórico de Agendamentos</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full table-modern text-left">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Médico</th>
                                <th>Especialidade</th>
                                <th>Status</th>
                                <th class="text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($consultas as $c): ?>
                            <tr class="table-row-modern transition-colors border-b border-slate-50 last:border-0">
                                <td class="font-medium text-slate-600">
                                    <?= date('d/m/Y', strtotime($c['data'])) ?>
                                </td>
                                <td class="font-bold text-slate-700"><?= $c['medico_nome'] ?></td>
                                <td class="text-slate-500"><?= $c['especialidade'] ?></td>
                                <td>
                                    <?php 
                                        $bg = match($c['status']) { 'cancelada' => 'bg-red-100 text-red-700', default => 'bg-green-100 text-green-700' };
                                    ?>
                                    <span class="<?= $bg ?> px-2 py-1 rounded text-xs font-bold uppercase tracking-wide">
                                        <?= $c['status'] ?>
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-2">
                                        
                                        <?php if($c['status'] == 'pendente' || $c['status'] == 'confirmada'): ?>
                                            <button onclick="prepararRemarcar('<?= $c['id'] ?>', '<?= $c['data'] ?>', '<?= $c['hora'] ?>')" class="p-2 text-blue-600 hover:bg-blue-50 rounded transition" title="Remarcar">
                                                <i data-lucide="calendar-days" class="w-4 h-4"></i>
                                            </button>

                                            <form action="index.php?action=cancelar" method="POST" onsubmit="return confirm('Tem certeza que deseja cancelar esta consulta?')" class="inline">
                                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded transition" title="Cancelar Agendamento">
                                                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                                                </button>
                                            </form>

                                        <?php elseif($c['status'] == 'finalizada' || $c['status'] == 'cancelada'): ?>
                                            <form action="index.php?action=excluir_consulta" method="POST" onsubmit="return confirm('Deseja remover este registro do seu histórico?')" class="inline">
                                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition" title="Excluir do Histórico">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>

                                        <?php endif; ?>
                                        
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- COLUNA LATERAL (Acesso Rápido) -->
        <div class="space-y-6">
            <div class="card-modern">
                <h3 class="text-lg font-bold text-slate-800 mb-4">Acesso Rápido</h3>
                <div class="space-y-2">
                    <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 text-slate-600 hover:text-blue-600 transition group">
                        <div class="bg-blue-100 p-2 rounded text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                        </div>
                        <span class="font-medium">Meus Prontuários</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 text-slate-600 hover:text-green-600 transition group">
                        <div class="bg-green-100 p-2 rounded text-green-600 group-hover:bg-green-600 group-hover:text-white transition">
                            <i data-lucide="flask-conical" class="w-4 h-4"></i>
                        </div>
                        <span class="font-medium">Resultados de Exames</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-50 text-slate-600 hover:text-yellow-600 transition group">
                        <div class="bg-yellow-100 p-2 rounded text-yellow-600 group-hover:bg-yellow-600 group-hover:text-white transition">
                            <i data-lucide="credit-card" class="w-4 h-4"></i>
                        </div>
                        <span class="font-medium">Financeiro</span>
                    </a>
                </div>
            </div>

            <!-- Banner Promocional / Aviso -->
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl p-6 text-white shadow-lg">
                <h4 class="font-bold text-lg mb-2">Campanha de Vacinação</h4>
                <p class="text-blue-100 text-sm mb-4">A vacina contra a gripe já está disponível em todas as unidades VidaPlus.</p>
                <button class="bg-white text-blue-700 text-xs font-bold px-3 py-2 rounded shadow-sm hover:bg-blue-50 transition">
                    Saiba mais
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    function prepararRemarcar(id, data, hora) {
        document.getElementById('input_id_remarcar').value = id;
        document.getElementById('input_data_remarcar').value = data;
        document.getElementById('input_hora_remarcar').value = hora;
        openModal('modal-remarcar');
    }
</script>