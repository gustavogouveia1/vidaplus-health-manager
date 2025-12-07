<?php include VIEW_PATH . 'layouts/header.php'; ?>

<div id="login-screen" class="flex-1 flex items-center justify-center p-4 fade-in bg-slate-100 relative w-full h-full">
    <!-- Fundo Decorativo -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[50vw] h-[50vw] rounded-full bg-blue-200/30 blur-3xl"></div>
        <div class="absolute top-[40%] -right-[10%] w-[40vw] h-[40vw] rounded-full bg-cyan-200/30 blur-3xl"></div>
    </div>

    <div class="bg-white w-full max-w-md rounded-2xl shadow-xl z-10 overflow-hidden border border-slate-200">
        <div class="bg-gradient-to-r from-blue-700 to-cyan-600 p-8 text-center transition-colors duration-500" id="header-bg">
            
            <!-- LOGOTIPO AJUSTADO -->
            <div class="mx-auto w-40 h-40 mb-6 flex items-center justify-center bg-white/10 rounded-full overflow-hidden backdrop-blur-sm shadow-inner border border-white/20">
                <img src="public/img/logo.svg" alt="VidaPlus Logo" class="w-full h-full object-cover">
            </div>

            <h1 class="text-2xl font-bold text-white tracking-tight" id="app-title">VidaPlus SGHSS</h1>
            <p class="text-blue-100 text-sm mt-1 font-light" id="app-subtitle">Acesso ao Sistema de Saúde</p>
        </div>

        <div class="p-8">
            <?php if(isset($_GET['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 text-sm">
                    Credenciais inválidas. Tente novamente.
                </div>
            <?php endif; ?>

            <form id="login-form" action="index.php?action=auth" method="POST" class="space-y-4" onsubmit="return validateForm(event)">
                <input type="hidden" name="tipo_usuario" id="tipo_usuario" value="paciente">
                
                <!-- Botões de Perfil -->
                <div class="flex bg-slate-100 p-1 rounded-lg mb-6" id="role-selector">
                    <button type="button" onclick="setRole('paciente')" id="btn-paciente" class="flex-1 py-2 text-sm font-bold rounded-md shadow-sm bg-white text-blue-700 transition-all">Sou Paciente</button>
                    <button type="button" onclick="setRole('medico')" id="btn-medico" class="flex-1 py-2 text-sm font-bold rounded-md text-slate-500 hover:text-slate-700 transition-all">Sou Médico</button>
                </div>

                <!-- Título Admin (Oculto por padrão) -->
                <div id="admin-title" class="hidden text-center mb-6">
                    <h3 class="text-lg font-bold text-slate-700">Acesso Administrativo</h3>
                    <p class="text-xs text-slate-500">Gestão do Sistema</p>
                </div>

                <div>
                    <!-- Label com ID para alteração via JS -->
                    <label id="label-usuario" class="block text-sm font-medium text-slate-700 mb-1">CPF</label>
                    <div class="relative">
                        <i data-lucide="user" class="absolute left-3 top-3 text-slate-400 w-5 h-5"></i>
                        <input type="text" id="input-usuario" name="usuario" placeholder="000.000.000-00" class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none uppercase transition-colors" required>
                    </div>
                    <p id="error-msg" class="text-red-500 text-xs mt-1 hidden font-medium flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3 h-3"></i> 
                        <span id="error-text">Documento inválido</span>
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Senha</label>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-3 top-3 text-slate-400 w-5 h-5"></i>
                        <input type="password" name="senha" placeholder="••••••••" class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                </div>

                <button type="submit" id="btn-submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 rounded-lg shadow-lg hover:shadow-xl transition-all transform active:scale-95 flex items-center justify-center gap-2">
                    <span>Entrar no Sistema</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <!-- Link para Admin -->
            <div class="mt-6 text-center border-t border-slate-100 pt-4">
                <button onclick="toggleAdminMode()" id="btn-toggle-admin" class="text-xs text-slate-400 hover:text-blue-600 font-medium transition flex items-center justify-center gap-1 mx-auto">
                    <i data-lucide="shield" class="w-3 h-3"></i>
                    <span id="admin-link-text">Acesso Administrativo</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let isAdminMode = false;

    // Configuração inicial
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('input-usuario');
        input.addEventListener('input', handleInputMask);
        input.addEventListener('input', () => {
            document.getElementById('input-usuario').classList.remove('border-red-500', 'bg-red-50');
            document.getElementById('input-usuario').classList.add('border-slate-300');
            document.getElementById('error-msg').classList.add('hidden');
        });
    });

    function toggleAdminMode() {
        isAdminMode = !isAdminMode;
        
        const roleSelector = document.getElementById('role-selector');
        const adminTitle = document.getElementById('admin-title');
        const headerBg = document.getElementById('header-bg');
        const label = document.getElementById('label-usuario');
        const input = document.getElementById('input-usuario');
        const submitBtn = document.getElementById('btn-submit');
        const linkText = document.getElementById('admin-link-text');
        const hiddenType = document.getElementById('tipo_usuario');

        // Limpa campos
        input.value = '';
        document.querySelector('input[name="senha"]').value = '';

        if (isAdminMode) {
            // Ativa Modo Admin
            roleSelector.classList.add('hidden');
            adminTitle.classList.remove('hidden');
            
            // Muda visual para cinza/escuro para diferenciar
            headerBg.classList.remove('from-blue-700', 'to-cyan-600');
            headerBg.classList.add('from-slate-700', 'to-slate-800');
            
            label.innerText = "Usuário Admin";
            input.placeholder = "admin";
            input.maxLength = 50; // Remove limite de CPF
            
            submitBtn.classList.remove('bg-blue-700', 'hover:bg-blue-800');
            submitBtn.classList.add('bg-slate-700', 'hover:bg-slate-800');
            
            linkText.innerText = "Voltar para Login de Usuário";
            hiddenType.value = "admin"; // Garante que o backend saiba
        } else {
            // Volta para Modo Normal
            roleSelector.classList.remove('hidden');
            adminTitle.classList.add('hidden');
            
            headerBg.classList.add('from-blue-700', 'to-cyan-600');
            headerBg.classList.remove('from-slate-700', 'to-slate-800');
            
            submitBtn.classList.add('bg-blue-700', 'hover:bg-blue-800');
            submitBtn.classList.remove('bg-slate-700', 'hover:bg-slate-800');
            
            linkText.innerText = "Acesso Administrativo";
            setRole('paciente'); // Reseta para paciente por padrão
        }
    }

    function validateForm(e) {
        if (isAdminMode) return true; // Admin não valida máscara

        const role = document.getElementById('tipo_usuario').value;
        const input = document.getElementById('input-usuario');
        const errorMsg = document.getElementById('error-msg');
        const errorText = document.getElementById('error-text');
        const value = input.value;

        let isValid = false;

        if (role === 'paciente') {
            if (value.length === 14 && /^\d{3}\.\d{3}\.\d{3}\-\d{2}$/.test(value)) {
                isValid = true;
            } else {
                errorText.innerText = "CPF incompleto ou inválido.";
            }
        } else {
            if (value.length >= 6) {
                isValid = true;
            } else {
                errorText.innerText = "Documento muito curto.";
            }
        }

        if (!isValid) {
            e.preventDefault();
            input.classList.remove('border-slate-300');
            input.classList.add('border-red-500', 'bg-red-50');
            errorMsg.classList.remove('hidden');
            input.parentElement.classList.add('animate-pulse');
            setTimeout(() => input.parentElement.classList.remove('animate-pulse'), 200);
            return false;
        }

        return true;
    }

    function setRole(role) {
        if (isAdminMode) return; 

        document.getElementById('tipo_usuario').value = role;
        const btnPac = document.getElementById('btn-paciente');
        const btnMed = document.getElementById('btn-medico');
        const label = document.getElementById('label-usuario');
        const input = document.getElementById('input-usuario');
        const errorMsg = document.getElementById('error-msg');
        
        input.value = '';
        input.classList.remove('border-red-500', 'bg-red-50');
        input.classList.add('border-slate-300');
        errorMsg.classList.add('hidden');

        if (role === 'paciente') {
            btnPac.className = "flex-1 py-2 text-sm font-bold rounded-md shadow-sm bg-white text-blue-700 transition-all";
            btnMed.className = "flex-1 py-2 text-sm font-bold rounded-md text-slate-500 hover:text-slate-700 transition-all";
            label.innerText = "CPF";
            input.placeholder = "000.000.000-00";
            input.maxLength = 14; 
        } else {
            btnMed.className = "flex-1 py-2 text-sm font-bold rounded-md shadow-sm bg-white text-blue-700 transition-all";
            btnPac.className = "flex-1 py-2 text-sm font-bold rounded-md text-slate-500 hover:text-slate-700 transition-all";
            label.innerText = "CPF / CRM";
            input.placeholder = "000.000.000-00 ou 000000/UF";
            input.maxLength = 14; 
        }
    }

    function handleInputMask(e) {
        if (isAdminMode) return; // Sem máscara para admin

        const role = document.getElementById('tipo_usuario').value;
        let value = e.target.value;

        if (role === 'paciente') {
            e.target.value = maskCPF(value);
        } else {
            if (/[a-zA-Z]/.test(value)) {
                e.target.value = maskCRM(value);
            } else {
                e.target.value = maskCPF(value);
            }
        }
    }

    function maskCPF(v) {
        v = v.replace(/\D/g, ""); 
        v = v.substring(0, 11); 
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        return v;
    }

    function maskCRM(v) {
        v = v.replace(/[^a-zA-Z0-9]/g, "");
        v = v.substring(0, 8); 
        let numbers = v.replace(/\D/g, '').substring(0, 6);
        let ufs = v.replace(/[^a-zA-Z]/g, '').substring(0, 2).toUpperCase();
        if (ufs.length > 0) return numbers + '/' + ufs;
        return numbers;
    }
</script>

<?php include VIEW_PATH . 'layouts/footer.php'; ?>