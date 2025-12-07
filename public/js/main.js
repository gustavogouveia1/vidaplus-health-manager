console.log("Sistema VidaPlus Carregado"); // Para debug no console

// Lógica do Menu Mobile
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
}

// Lógica de Modais (ABRIR E FECHAR)
function openModal(id) {
    const modal = document.getElementById(id);
    if(modal) {
        modal.classList.remove('hidden-screen');
        // Pequena animação de entrada
        setTimeout(() => {
            const content = modal.querySelector('div.transform');
            if(content) {
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }
        }, 10);
    } else {
        console.error("Modal não encontrado: " + id);
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if(modal) {
        modal.classList.add('hidden-screen');
        const content = modal.querySelector('div.transform');
        if(content) {
            content.classList.remove('scale-100');
            content.classList.add('scale-95');
        }
    }
}

// Preenche o modal de remarcar com os dados atuais
function prepararRemarcar(id, data, hora) {
    const inputId = document.getElementById('input_id_remarcar');
    const inputData = document.getElementById('input_data_remarcar');
    const inputHora = document.getElementById('input_hora_remarcar');

    if(inputId && inputData && inputHora) {
        inputId.value = id;
        inputData.value = data;
        
        // Ajusta hora para formato HH:MM (caso venha com segundos do banco)
        const horaFormatada = hora.substring(0, 5); 
        inputHora.value = horaFormatada;
        
        openModal('modal-remarcar');
    } else {
        console.error("Campos do modal de remarcar não encontrados.");
    }
}