<!-- Modal Agendamento (Novo) -->
<div id="modal-agendar" class="hidden-screen fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modal-agendar')"></div>
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl relative z-10 transform scale-100 transition-all">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800">Nova Consulta</h3>
            <button onclick="closeModal('modal-agendar')" class="text-slate-400 hover:text-red-500"><i data-lucide="x"></i></button>
        </div>
        <form action="index.php?action=agendar" method="POST">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Especialidade</label>
                    <select name="especialidade" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="Cardiologia">Cardiologia</option>
                        <option value="Dermatologia">Dermatologia</option>
                        <option value="Clínico Geral">Clínico Geral</option>
                        <option value="Pediatria">Pediatria</option>
                        <option value="Ortopedia">Ortopedia</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Data</label>
                        <input type="date" name="data" id="agendar_data" min="<?= date('Y-m-d') ?>" class="w-full border border-slate-300 rounded-lg p-2 outline-none focus:ring-2 focus:ring-blue-500" required onchange="validarHorarios('agendar_data', 'agendar_hora')">
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Horário</label>
                        <select name="hora" id="agendar_hora" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="08:00">08:00</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="11:00">11:00</option>
                            <option value="14:00">14:00</option>
                            <option value="15:30">15:30</option>
                            <option value="16:30">16:30</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="p-6 bg-slate-50 rounded-b-2xl flex justify-end gap-3">
                <button type="button" onclick="closeModal('modal-agendar')" class="px-4 py-2 text-slate-600 hover:bg-slate-200 rounded-lg font-medium transition-colors">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow-md transition-colors">Confirmar Agendamento</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Remarcar (Edição) -->
<div id="modal-remarcar" class="hidden-screen fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modal-remarcar')"></div>
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl relative z-10 transform scale-100 transition-all">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800">Remarcar Consulta</h3>
            <button onclick="closeModal('modal-remarcar')" class="text-slate-400 hover:text-red-500"><i data-lucide="x"></i></button>
        </div>
        <form action="index.php?action=remarcar" method="POST">
            <input type="hidden" name="id_remarcar" id="input_id_remarcar">

            <div class="p-6 space-y-4">
                <div class="bg-blue-50 p-3 rounded-lg text-sm text-blue-700 border border-blue-200 flex items-start gap-2">
                    <i data-lucide="info" class="w-4 h-4 mt-0.5"></i>
                    <div>Selecione a nova data e horário desejado.</div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nova Data</label>
                        <input type="date" name="data" id="input_data_remarcar" min="<?= date('Y-m-d') ?>" class="w-full border border-slate-300 rounded-lg p-2 outline-none focus:ring-2 focus:ring-blue-500" required onchange="validarHorarios('input_data_remarcar', 'input_hora_remarcar')">
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Novo Horário</label>
                        <select name="hora" id="input_hora_remarcar" class="w-full border border-slate-300 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="08:00">08:00</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="11:00">11:00</option>
                            <option value="14:00">14:00</option>
                            <option value="15:30">15:30</option>
                            <option value="16:30">16:30</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="p-6 bg-slate-50 rounded-b-2xl flex justify-end gap-3">
                <button type="button" onclick="closeModal('modal-remarcar')" class="px-4 py-2 text-slate-600 hover:bg-slate-200 rounded-lg font-medium transition-colors">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow-md transition-colors">Salvar Alteração</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Prescrição (Médico) -->
<div id="modal-prescricao" class="hidden-screen fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modal-prescricao')"></div>
    <div class="bg-white rounded-2xl w-full max-w-xl shadow-2xl relative z-10">
        <form action="index.php?action=prescrever" method="POST">
            <div class="p-6 border-b border-slate-100 bg-blue-600 rounded-t-2xl text-white flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold">Emitir Receita Digital</h3>
                    <p class="text-blue-100 text-xs">Sistema Integrado</p>
                </div>
                <button type="button" onclick="closeModal('modal-prescricao')" class="text-white/80 hover:text-white"><i data-lucide="x"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <p class="text-sm text-slate-500 mb-4">Esta funcionalidade simula o envio de uma receita para o sistema.</p>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Medicamento</label>
                    <input type="text" name="medicamento" placeholder="Nome do fármaco" class="w-full border border-slate-300 rounded-lg p-2 outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="p-6 bg-slate-50 rounded-b-2xl flex justify-end gap-3">
                <button type="button" onclick="closeModal('modal-prescricao')" class="px-4 py-2 text-slate-600 hover:bg-slate-200 rounded-lg font-medium">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 shadow-md flex items-center gap-2">
                    <i data-lucide="send" class="w-4 h-4"></i> Emitir
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Cadastro Médico (ADMIN) -->
<div id="modal-cadastro-medico" class="hidden-screen fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modal-cadastro-medico')"></div>
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl relative z-10 transform scale-100 transition-all">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800">Cadastrar Médico</h3>
            <button onclick="closeModal('modal-cadastro-medico')" class="text-slate-400 hover:text-red-500"><i data-lucide="x"></i></button>
        </div>
        <form action="index.php?action=cadastrar_medico" method="POST">
            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <div><label class="block text-sm font-medium mb-1">Nome Completo</label><input type="text" name="nome" class="w-full border p-2 rounded" required></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium mb-1">CPF</label><input type="text" name="cpf" class="w-full border p-2 rounded" required></div>
                    <div><label class="block text-sm font-medium mb-1">CRM</label><input type="text" name="crm" class="w-full border p-2 rounded" required></div>
                </div>
                <div><label class="block text-sm font-medium mb-1">Especialidade Principal</label>
                    <select name="especialidade" class="w-full border p-2 rounded">
                        <option>Cardiologia</option><option>Ortopedia</option><option>Pediatria</option><option>Clínico Geral</option>
                    </select>
                </div>
                <div><label class="block text-sm font-medium mb-1">Email</label><input type="email" name="email" class="w-full border p-2 rounded"></div>
                <div><label class="block text-sm font-medium mb-1">Senha de Acesso</label><input type="password" name="senha" class="w-full border p-2 rounded" required></div>
            </div>
            <div class="p-6 bg-slate-50 rounded-b-2xl flex justify-end gap-3">
                <button type="button" onclick="closeModal('modal-cadastro-medico')" class="px-4 py-2 text-slate-600 bg-white border rounded">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded font-bold hover:bg-blue-700">Cadastrar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Cadastro Paciente (ADMIN) -->
<div id="modal-cadastro-paciente" class="hidden-screen fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modal-cadastro-paciente')"></div>
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl relative z-10 transform scale-100 transition-all">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800">Cadastrar Paciente</h3>
            <button onclick="closeModal('modal-cadastro-paciente')" class="text-slate-400 hover:text-red-500"><i data-lucide="x"></i></button>
        </div>
        <form action="index.php?action=cadastrar_paciente" method="POST">
            <div class="p-6 space-y-4">
                <div><label class="block text-sm font-medium mb-1">Nome Completo</label><input type="text" name="nome" class="w-full border p-2 rounded" required></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium mb-1">CPF</label><input type="text" name="cpf" class="w-full border p-2 rounded" required></div>
                    <div><label class="block text-sm font-medium mb-1">Telefone</label><input type="text" name="telefone" class="w-full border p-2 rounded"></div>
                </div>
                <div><label class="block text-sm font-medium mb-1">Email</label><input type="email" name="email" class="w-full border p-2 rounded"></div>
                <div><label class="block text-sm font-medium mb-1">Senha de Acesso</label><input type="password" name="senha" class="w-full border p-2 rounded" required></div>
            </div>
            <div class="p-6 bg-slate-50 rounded-b-2xl flex justify-end gap-3">
                <button type="button" onclick="closeModal('modal-cadastro-paciente')" class="px-4 py-2 text-slate-600 bg-white border rounded">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded font-bold hover:bg-green-700">Cadastrar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Prontuário (Estilo Imagem Enviada) -->
<div id="modal-prontuario" class="hidden-screen fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modal-prontuario')"></div>
    
    <!-- Modal Card -->
    <div class="bg-white rounded-xl w-full max-w-4xl shadow-2xl relative z-10 transform scale-100 transition-all flex flex-col max-h-[90vh]">
        
        <!-- Header -->
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50 rounded-t-xl">
            <h3 class="text-xl font-bold text-slate-800">Prontuário Eletrônico</h3>
            <button onclick="closeModal('modal-prontuario')" class="text-slate-400 hover:text-red-500"><i data-lucide="x"></i></button>
        </div>

        <!-- Conteúdo Rolável -->
        <div class="flex-1 overflow-y-auto p-6 bg-slate-50/50">
            
            <!-- Resumo do Paciente -->
            <div class="bg-white border border-slate-200 rounded-lg p-6 mb-6 shadow-sm flex flex-col md:flex-row gap-6 items-center md:items-start">
                <div class="w-20 h-20 rounded-full bg-slate-200 overflow-hidden flex-shrink-0 border-2 border-slate-100">
                    <img src="https://ui-avatars.com/api/?name=Paciente&background=random&color=fff" id="img_paciente_prontuario" alt="Paciente" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h2 class="text-2xl font-bold text-slate-800 mb-1" id="prontuario_paciente_nome">Nome do Paciente</h2>
                    <div class="text-sm text-slate-500 space-y-1">
                        <p>Idade: <span class="font-medium text-slate-700">32 anos</span> • Convênio: <span class="font-medium text-slate-700">Unimed</span></p>
                        <p>Primeira consulta: 29/10/2017 • Atendimentos: 2</p>
                    </div>
                    <button class="mt-3 text-xs font-bold text-blue-600 border border-blue-200 bg-blue-50 px-3 py-1 rounded-full hover:bg-blue-100 transition">
                        + Adicionar tag
                    </button>
                </div>
                <div>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2 rounded shadow-sm transition">
                        VISUALIZAR CADASTRO
                    </button>
                </div>
            </div>          

            <!-- Toolbar -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-500 uppercase">Filtrar:</span>
                    <select class="bg-white border border-slate-200 text-sm rounded px-2 py-1 outline-none focus:ring-1 focus:ring-blue-500">
                        <option>Todos</option>
                        <option>Consultas</option>
                        <option>Exames</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button class="flex items-center gap-1 text-xs font-bold text-slate-500 bg-white border border-slate-200 px-3 py-1.5 rounded hover:bg-slate-50 transition">
                        <i data-lucide="share-2" class="w-3 h-3"></i> COMPARTILHAR
                    </button>
                    <button class="flex items-center gap-1 text-xs font-bold text-slate-500 bg-white border border-slate-200 px-3 py-1.5 rounded hover:bg-slate-50 transition">
                        <i data-lucide="printer" class="w-3 h-3"></i> IMPRIMIR
                    </button>
                </div>
            </div>

            <!-- Timeline -->
            <div class="relative pl-8 border-l-2 border-slate-200 space-y-8">
                
                <!-- Item da Timeline -->
                <div class="relative">
                    <!-- Data Badge -->
                    <div class="absolute -left-[42px] top-0 bg-blue-500 text-white text-center rounded w-16 py-1 shadow-sm z-10">
                        <span class="block text-xl font-bold leading-none">27</span>
                        <span class="block text-[10px] font-bold uppercase">NOV 2025</span>
                    </div>
                    <!-- Bolinha Conector -->
                    <div class="absolute -left-[9px] top-4 w-4 h-4 bg-white border-2 border-blue-500 rounded-full z-10"></div>

                    <!-- Card de Conteúdo -->
                    <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                        <!-- Header do Card -->
                        <div class="bg-slate-50 px-4 py-3 border-b border-slate-100 flex justify-between items-center">
                            <div class="flex items-center gap-2 text-sm font-bold text-slate-700">
                                <span>Por: Dr. José Rodrigues</span>
                                <i data-lucide="lock" class="w-3 h-3 text-slate-400"></i>
                            </div>
                            <div class="flex items-center gap-1 text-xs text-slate-400">
                                <i data-lucide="clock" class="w-3 h-3"></i> 10:00
                            </div>
                        </div>
                        
                        <!-- Corpo do Card -->
                        <div class="p-6">
                            <h4 class="text-sm font-bold text-blue-500 mb-4 uppercase tracking-wide">Retorno</h4>
                            
                            <div class="space-y-3 max-w-xs">
                                <div class="flex items-center justify-between p-2 hover:bg-slate-50 rounded group transition">
                                    <span class="text-slate-600 font-medium">Peso:</span>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-800">99,876 kg</span>
                                        <i data-lucide="edit-2" class="w-3 h-3 text-slate-300 group-hover:text-blue-500 cursor-pointer"></i>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-2 hover:bg-slate-50 rounded group transition">
                                    <span class="text-slate-600 font-medium">Altura:</span>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-800">178 cm</span>
                                        <i data-lucide="edit-2" class="w-3 h-3 text-slate-300 group-hover:text-blue-500 cursor-pointer"></i>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between p-2 hover:bg-slate-50 rounded group transition">
                                    <span class="text-slate-600 font-medium">IMC:</span>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-800">31,2</span>
                                        <i data-lucide="edit-2" class="w-3 h-3 text-slate-300 group-hover:text-blue-500 cursor-pointer"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer do Card -->
                        <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                            <button class="text-xs font-bold text-slate-500 hover:text-blue-600 flex items-center gap-1 bg-white border border-slate-200 px-3 py-1.5 rounded shadow-sm transition">
                                <i data-lucide="plus-circle" class="w-3 h-3"></i> INSERIR INFORMAÇÕES
                            </button>
                            <button class="p-1.5 text-slate-400 hover:text-slate-600 bg-white border border-slate-200 rounded shadow-sm transition">
                                <i data-lucide="printer" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
// Validar Horários (Mantido do anterior)
function validarHorarios(inputIdData, selectIdHora) {
    const inputData = document.getElementById(inputIdData);
    const selectHora = document.getElementById(selectIdHora);
    
    if (!inputData || !selectHora) return;
    if(!inputData.value) return;

    const partesData = inputData.value.split('-');
    const dataSelecionada = new Date(partesData[0], partesData[1] - 1, partesData[2]);
    const hoje = new Date();
    const hojeSemHora = new Date(hoje.getFullYear(), hoje.getMonth(), hoje.getDate());

    if (dataSelecionada.getTime() === hojeSemHora.getTime()) {
        const horaAtual = hoje.getHours();
        const minutoAtual = hoje.getMinutes();

        Array.from(selectHora.options).forEach(option => {
            const [horaOpt, minutoOpt] = option.value.split(':').map(Number);
            if (horaOpt < horaAtual || (horaOpt === horaAtual && minutoOpt <= minutoAtual)) {
                option.disabled = true;
                option.style.color = '#ccc'; 
                if(selectHora.value === option.value) selectHora.value = "";
            } else {
                option.disabled = false;
                option.style.color = '';
            }
        });
    } else {
        Array.from(selectHora.options).forEach(option => {
            option.disabled = false;
            option.style.color = '';
        });
    }
}

// Abrir Modal de Prontuário com Nome Dinâmico
function abrirProntuario(nomePaciente) {
    // Atualiza o nome no modal
    document.getElementById('prontuario_paciente_nome').innerText = nomePaciente;
    // Atualiza a imagem (avatar gerado com as iniciais)
    document.getElementById('img_paciente_prontuario').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(nomePaciente)}&background=random&color=fff`;
    
    openModal('modal-prontuario');
}
</script>

<!-- Modal Finalizar Atendimento (Médico) -->
<div id="modal-finalizar" class="hidden-screen fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modal-finalizar')"></div>
    
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl relative z-10 transform scale-100 transition-all">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-green-50 rounded-t-2xl">
            <h3 class="text-lg font-bold text-green-800 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i> Finalizar Atendimento
            </h3>
            <button onclick="closeModal('modal-finalizar')" class="text-slate-400 hover:text-red-500"><i data-lucide="x"></i></button>
        </div>

        <form action="index.php?action=finalizar_consulta" method="POST">
            <input type="hidden" name="id_consulta" id="input_id_finalizar">

            <div class="p-6 space-y-6">
                <p class="text-slate-600 text-sm">Deseja encerrar este atendimento? O status será alterado para <span class="font-bold text-green-600">Finalizado</span>.</p>
                
                <!-- Opção de Retorno -->
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <label class="flex items-center gap-3 cursor-pointer mb-4">
                        <input type="checkbox" name="marcar_retorno" id="check_retorno" class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500" onchange="toggleCamposRetorno()">
                        <span class="font-bold text-slate-700 text-sm">Agendar Retorno (Sem Custo)</span>
                    </label>

                    <!-- Campos de Retorno (Ocultos inicialmente) -->
                    <div id="campos_retorno" class="hidden space-y-4 pt-2 border-t border-slate-200">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Data</label>
                                <input type="date" name="data_retorno" min="<?= date('Y-m-d') ?>" class="w-full border border-slate-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Horário</label>
                                <select name="hora_retorno" class="w-full border border-slate-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                                    <option value="09:00">09:00</option>
                                    <option value="10:00">10:00</option>
                                    <option value="14:00">14:00</option>
                                    <option value="16:00">16:00</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-slate-50 rounded-b-2xl flex justify-end gap-3">
                <button type="button" onclick="closeModal('modal-finalizar')" class="px-4 py-2 text-slate-600 hover:bg-slate-200 rounded-lg font-medium transition">Cancelar</button>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 shadow-md transition flex items-center gap-2">
                    <i data-lucide="check"></i> Confirmar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleCamposRetorno() {
    const check = document.getElementById('check_retorno');
    const campos = document.getElementById('campos_retorno');
    if (check.checked) {
        campos.classList.remove('hidden');
    } else {
        campos.classList.add('hidden');
    }
}

// Abre o modal e seta o ID da consulta
function abrirModalFinalizar(idConsulta) {
    document.getElementById('input_id_finalizar').value = idConsulta;
    // Reseta o form
    document.getElementById('check_retorno').checked = false;
    document.getElementById('campos_retorno').classList.add('hidden');
    
    openModal('modal-finalizar');
}
</script> 