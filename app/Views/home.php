<?php include 'layouts/header.php'; ?>

<!-- Wrapper Principal -->
<div id="app-layout" class="flex h-screen bg-slate-50 w-full fade-in">
    
    <!-- SIDEBAR -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 z-20 mobile-overlay hidden lg:hidden transition-opacity"></div>

    <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-30 w-64 bg-white border-r border-slate-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col">
        <div class="h-16 flex items-center gap-3 px-6 border-b border-slate-100">
            <!-- LOGOTIPO NO MENU -->
            <img src="public/img/logo.svg" alt="VidaPlus Logo" class="w-8 h-8 object-contain">
            <span class="font-bold text-lg text-slate-800">VidaPlus</span>
            <button onclick="toggleSidebar()" class="ml-auto lg:hidden text-slate-400"><i data-lucide="x"></i></button>
        </div>

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 mt-2">Menu Principal</p>
            
            <a href="?page=home" class="flex items-center gap-3 px-4 py-3 bg-blue-50 text-blue-700 rounded-lg font-medium transition-colors">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
            </a>

            <!-- Menu Diferenciado -->
            <?php if($_SESSION['tipo_usuario'] == 'medico'): ?>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition-colors">
                    <i data-lucide="users" class="w-5 h-5"></i> Meus Pacientes
                </a>
            <?php elseif($_SESSION['tipo_usuario'] == 'admin'): ?>
                <a href="#" onclick="openModal('modal-cadastro-medico')" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition-colors">
                    <i data-lucide="user-plus" class="w-5 h-5"></i> Novo Médico
                </a>
                <a href="#" onclick="openModal('modal-cadastro-paciente')" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition-colors">
                    <i data-lucide="user-plus" class="w-5 h-5"></i> Novo Paciente
                </a>
            <?php else: ?>
                 <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition-colors">
                    <i data-lucide="calendar" class="w-5 h-5"></i> Minha Agenda
                </a>
            <?php endif; ?>

            <a href="#" class="flex items-center gap-3 px-4 py-3 text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition-colors">
                <i data-lucide="video" class="w-5 h-5"></i> Telemedicina
            </a>
        </nav>

        <div class="p-4 border-t border-slate-100">
            <a href="?action=logout" class="flex items-center gap-3 px-4 py-2 w-full text-red-600 hover:bg-red-50 rounded-lg transition-colors text-sm font-medium">
                <i data-lucide="log-out" class="w-5 h-5"></i> Sair
            </a>
        </div>
    </aside>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 lg:px-8 shadow-sm relative z-10">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="lg:hidden text-slate-500 hover:text-blue-600 p-1">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <h2 class="text-lg lg:text-xl font-bold text-slate-800 truncate">
                    Olá, <?= $_SESSION['nome'] ?>
                </h2>
            </div>
            <div class="flex items-center gap-3 lg:gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-slate-800"><?= $_SESSION['nome'] ?></p>
                    <p class="text-xs text-slate-500 uppercase"><?= $_SESSION['tipo_usuario'] ?></p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold border border-blue-200">
                    <?= substr($_SESSION['nome'], 0, 1) ?>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-4 lg:p-8 relative scroll-smooth">
            
            <?php if($_SESSION['tipo_usuario'] == 'paciente'): ?>
                <?php include 'partials/dashboard_paciente.php'; ?>
            <?php elseif($_SESSION['tipo_usuario'] == 'medico'): ?>
                <?php include 'partials/dashboard_medico.php'; ?>
            <?php else: ?>
                <!-- VIEW ADMIN -->
                <?php include 'partials/dashboard_admin.php'; ?>
            <?php endif; ?>

        </div>
    </main>
</div>

<!-- Incluir Modais -->
<?php include 'partials/modais.php'; ?>

<?php include 'layouts/footer.php'; ?>