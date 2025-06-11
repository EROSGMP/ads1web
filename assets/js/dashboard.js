// Variáveis globais
let usuarioLogado = null;
let simulacaoRapida = null;

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    // Verificar se usuário está logado
    const savedUser = localStorage.getItem('newbank_user');
    if (!savedUser) {
        window.location.href = 'index.html';
        return;
    }
    
    usuarioLogado = JSON.parse(savedUser);
    
    // Carregar dados do dashboard
    carregarDashboard();
    
    // Event listeners
    document.getElementById('updateForm').addEventListener('submit', handleUpdateForm);
});

// Carregar dados do dashboard
function carregarDashboard() {
    // Atualizar informações do usuário
    document.getElementById('userName').textContent = `Olá, ${usuarioLogado.nome.split(' ')[0]}`;
    document.getElementById('clienteName').textContent = usuarioLogado.nome.split(' ')[0];
    
    // Calcular limite disponível baseado na renda (até 10x da renda, máximo R$ 50.000)
    const limite = Math.min(usuarioLogado.renda_mensal * 10, 50000);
    document.getElementById('limiteDisponivel').textContent = formatarMoeda(limite);
    
    // Carregar empréstimos
    carregarEmprestimos();
    
    // Carregar simulações
    carregarSimulacoes();
}

// Carregar empréstimos do cliente
function carregarEmprestimos() {
    fetch(`api/obter_emprestimos.php?cliente_id=${usuarioLogado.id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                exibirEmprestimos(data.emprestimos);
                document.getElementById('emprestimosAtivos').textContent = data.emprestimos.filter(e => e.status === 'aprovado').length;
            }
        })
        .catch(error => {
            console.error('Erro ao carregar empréstimos:', error);
        });
}

// Carregar simulações do cliente
function carregarSimulacoes() {
    fetch(`api/obter_simulacoes.php?cliente_id=${usuarioLogado.id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                exibirSimulacoes(data.simulacoes);
                document.getElementById('totalSimulacoes').textContent = data.simulacoes.length;
            }
        })
        .catch(error => {
            console.error('Erro ao carregar simulações:', error);
        });
}

// Exibir empréstimos
function exibirEmprestimos(emprestimos) {
    const container = document.getElementById('emprestimosList');
    
    if (emprestimos.length === 0) {
        container.innerHTML = `
            <div class="no-data">
                <i class="fas fa-inbox"></i>
                <p>Nenhum empréstimo encontrado</p>
            </div>
        `;
        return;
    }
    
    const html = emprestimos.map(emprestimo => `
        <div class="loan-item">
            <div class="loan-header">
                <h4>Empréstimo #${emprestimo.id}</h4>
                <span class="loan-status status-${emprestimo.status}">${getStatusText(emprestimo.status)}</span>
            </div>
            <div class="loan-details">
                <div class="detail-item">
                    <div class="label">Valor Solicitado</div>
                    <div class="value">${formatarMoeda(emprestimo.valor_solicitado)}</div>
                </div>
                <div class="detail-item">
                    <div class="label">Parcelas</div>
                    <div class="value">${emprestimo.prazo_meses}x ${formatarMoeda(emprestimo.valor_parcela)}</div>
                </div>
                <div class="detail-item">
                    <div class="label">Taxa</div>
                    <div class="value">${parseFloat(emprestimo.taxa_juros).toFixed(2)}% a.m.</div>
                </div>
                <div class="detail-item">
                    <div class="label">Data</div>
                    <div class="value">${formatarData(emprestimo.data_solicitacao)}</div>
                </div>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = html;
}

// Exibir simulações
function exibirSimulacoes(simulacoes) {
    const container = document.getElementById('simulacoesList');
    
    if (simulacoes.length === 0) {
        container.innerHTML = `
            <div class="no-data">
                <i class="fas fa-calculator"></i>
                <p>Nenhuma simulação encontrada</p>
            </div>
        `;
        return;
    }
    
    const html = simulacoes.map(simulacao => `
        <div class="simulation-item">
            <div class="simulation-header">
                <h4>Simulação ${formatarData(simulacao.created_at)}</h4>
                <button class="btn btn-primary btn-small" onclick="repetirSimulacao(${simulacao.id})">
                    <i class="fas fa-redo"></i> Repetir
                </button>
            </div>
            <div class="simulation-details">
                <div class="detail-item">
                    <div class="label">Valor</div>
                    <div class="value">${formatarMoeda(simulacao.valor)}</div>
                </div>
                <div class="detail-item">
                    <div class="label">Prazo</div>
                    <div class="value">${simulacao.prazo_meses} meses</div>
                </div>
                <div class="detail-item">
                    <div class="label">Parcela</div>
                    <div class="value">${formatarMoeda(simulacao.valor_parcela)}</div>
                </div>
                <div class="detail-item">
                    <div class="label">Taxa</div>
                    <div class="value">${parseFloat(simulacao.taxa_juros).toFixed(2)}% a.m.</div>
                </div>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = html;
}

// Simulação rápida
function simularRapido() {
    const valor = parseFloat(document.getElementById('valorRapido').value);
    const prazo = parseInt(document.getElementById('prazoRapido').value);
    
    if (!valor || valor < 500 || valor > 100000) {
        alert('Por favor, insira um valor entre R$ 500 e R$ 100.000');
        return;
    }
    
    // Usar a mesma lógica de cálculo da página principal
    const taxa = calcularTaxa(valor, prazo);
    const valorTotal = valor * Math.pow(1 + taxa/100, prazo);
    const valorParcela = valorTotal / prazo;
    
    simulacaoRapida = {
        valor: valor,
        prazo: prazo,
        taxa: taxa,
        valorParcela: valorParcela,
        valorTotal: valorTotal
    };
    
    // Exibir resultado
    document.getElementById('parcelaRapida').textContent = formatarMoeda(valorParcela);
    document.getElementById('taxaRapida').textContent = taxa.toFixed(2) + '% a.m.';
    document.getElementById('resultadoRapido').style.display = 'block';
    
    // Salvar simulação
    salvarSimulacao(simulacaoRapida);
}

function calcularTaxa(valor, prazo) {
    let taxaBase = 2.5;
    
    if (valor >= 50000) taxaBase -= 0.8;
    else if (valor >= 20000) taxaBase -= 0.5;
    else if (valor >= 10000) taxaBase -= 0.3;
    
    if (prazo >= 36) taxaBase -= 0.4;
    else if (prazo >= 24) taxaBase -= 0.2;
    
    return Math.max(taxaBase, 1.99);
}

// Solicitar empréstimo rápido
function solicitarRapido() {
    if (!simulacaoRapida) {
        alert('Por favor, faça uma simulação primeiro.');
        return;
    }
    
    fetch('api/solicitar_emprestimo.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cliente_id: usuarioLogado.id,
            valor_solicitado: simulacaoRapida.valor,
            taxa_juros: simulacaoRapida.taxa,
            prazo_meses: simulacaoRapida.prazo,
            valor_parcela: simulacaoRapida.valorParcela,
            valor_total: simulacaoRapida.valorTotal
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Solicitação enviada com sucesso!');
            carregarEmprestimos(); // Recarregar lista
            document.getElementById('resultadoRapido').style.display = 'none';
            document.getElementById('valorRapido').value = '';
        } else {
            alert('Erro ao solicitar empréstimo: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao processar solicitação.');
    });
}

// Salvar simulação
function salvarSimulacao(simulacao) {
    fetch('api/salvar_simulacao.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cliente_id: usuarioLogado.id,
            valor: simulacao.valor,
            prazo_meses: simulacao.prazo,
            taxa_juros: simulacao.taxa,
            valor_parcela: simulacao.valorParcela,
            valor_total: simulacao.valorTotal
        })
    })
    .then(() => {
        carregarSimulacoes(); // Recarregar lista
    })
    .catch(error => {
        console.error('Erro ao salvar simulação:', error);
    });
}

// Ações rápidas
function novaSimulacao() {
    window.location.href = 'index.html#simulador';
}

function verEmprestimos() {
    document.querySelector('.loans-section').scrollIntoView({
        behavior: 'smooth'
    });
}

function atualizarDados() {
    // Preencher formulário com dados atuais
    document.getElementById('updateNome').value = usuarioLogado.nome;
    document.getElementById('updateTelefone').value = usuarioLogado.telefone;
    document.getElementById('updateRenda').value = usuarioLogado.renda_mensal;
    document.getElementById('updateEndereco').value = usuarioLogado.endereco || '';
    
    document.getElementById('updateModal').style.display = 'block';
}

function suporte() {
    alert('Em breve você será redirecionado para nossa central de atendimento!');
}

// Atualizar dados do cliente
function handleUpdateForm(e) {
    e.preventDefault();
    
    const dadosAtualizados = {
        id: usuarioLogado.id,
        nome: document.getElementById('updateNome').value,
        telefone: document.getElementById('updateTelefone').value,
        renda_mensal: parseFloat(document.getElementById('updateRenda').value),
        endereco: document.getElementById('updateEndereco').value
    };
    
    fetch('api/atualizar_cliente.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(dadosAtualizados)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Atualizar dados locais
            usuarioLogado.nome = dadosAtualizados.nome;
            usuarioLogado.telefone = dadosAtualizados.telefone;
            usuarioLogado.renda_mensal = dadosAtualizados.renda_mensal;
            usuarioLogado.endereco = dadosAtualizados.endereco;
            
            localStorage.setItem('newbank_user', JSON.stringify(usuarioLogado));
            
            alert('Dados atualizados com sucesso!');
            closeModal('updateModal');
            carregarDashboard(); // Recarregar dashboard
        } else {
            alert('Erro ao atualizar dados: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao atualizar dados.');
    });
}

// Repetir simulação
function repetirSimulacao(simulacaoId) {
    // Buscar dados da simulação e preencher formulário
    fetch(`api/obter_simulacao.php?id=${simulacaoId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const simulacao = data.simulacao;
                document.getElementById('valorRapido').value = simulacao.valor;
                document.getElementById('prazoRapido').value = simulacao.prazo_meses;
                simularRapido();
                
                // Scroll para o simulador
                document.querySelector('.quick-simulator').scrollIntoView({
                    behavior: 'smooth'
                });
            }
        })
        .catch(error => {
            console.error('Erro:', error);
        });
}

// Logout
function logout() {
    localStorage.removeItem('newbank_user');
    window.location.href = 'index.html';
}

// Modal functions
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Utility functions
function formatarMoeda(valor) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(valor);
}

function formatarData(dataString) {
    const data = new Date(dataString);
    return data.toLocaleDateString('pt-BR');
}

function getStatusText(status) {
    const statusMap = {
        'pendente': 'Pendente',
        'aprovado': 'Aprovado',
        'rejeitado': 'Rejeitado',
        'pago': 'Pago'
    };
    return statusMap[status] || status;
}

// Fechar modal clicando fora
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}); 