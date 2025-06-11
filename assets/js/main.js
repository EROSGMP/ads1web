// Variáveis globais
let simulacaoAtual = null;
let usuarioLogado = null;

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    // Aplicar máscaras
    aplicarMascaras();
    
    // Event listeners para formulários
    document.getElementById('loginForm').addEventListener('submit', handleLogin);
    document.getElementById('registerForm').addEventListener('submit', handleRegister);
    
    // Smooth scroll para navegação
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Verificar se há usuário logado
    const savedUser = localStorage.getItem('newbank_user');
    if (savedUser) {
        usuarioLogado = JSON.parse(savedUser);
        updateUIForLoggedUser();
    }
});

// Funções de Modal
function showLogin() {
    closeAllModals();
    document.getElementById('loginModal').style.display = 'block';
}

function showRegister() {
    closeAllModals();
    document.getElementById('registerModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function closeAllModals() {
    document.querySelectorAll('.modal').forEach(modal => {
        modal.style.display = 'none';
    });
}

// Fechar modal clicando fora
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
});

// Função para mostrar simulador
function showSimulator() {
    document.getElementById('simulador').scrollIntoView({
        behavior: 'smooth'
    });
}

// Simulação de empréstimo
function simularEmprestimo() {
    const valor = parseFloat(document.getElementById('valor').value);
    const prazo = parseInt(document.getElementById('prazo').value);
    
    if (!valor || valor < 500 || valor > 100000) {
        alert('Por favor, insira um valor entre R$ 500 e R$ 100.000');
        return;
    }
    
    // Cálculo da taxa baseada no valor e prazo
    let taxa = calcularTaxa(valor, prazo);
    
    // Cálculo das parcelas usando juros compostos
    const valorTotal = valor * Math.pow(1 + taxa/100, prazo);
    const valorParcela = valorTotal / prazo;
    
    // Armazenar simulação
    simulacaoAtual = {
        valor: valor,
        prazo: prazo,
        taxa: taxa,
        valorParcela: valorParcela,
        valorTotal: valorTotal
    };
    
    // Exibir resultado
    document.getElementById('valorSolicitado').textContent = formatarMoeda(valor);
    document.getElementById('taxaJuros').textContent = taxa.toFixed(2) + '% a.m.';
    document.getElementById('numParcelas').textContent = prazo + 'x de';
    document.getElementById('valorParcela').textContent = formatarMoeda(valorParcela);
    document.getElementById('valorTotal').textContent = formatarMoeda(valorTotal);
    
    document.getElementById('resultado').style.display = 'block';
    
    // Salvar simulação no backend (se usuário logado)
    if (usuarioLogado) {
        salvarSimulacao(simulacaoAtual);
    }
}

function calcularTaxa(valor, prazo) {
    // Lógica de taxa baseada no valor e prazo
    let taxaBase = 2.5; // Taxa base de 2.5%
    
    // Desconto por valor alto
    if (valor >= 50000) taxaBase -= 0.8;
    else if (valor >= 20000) taxaBase -= 0.5;
    else if (valor >= 10000) taxaBase -= 0.3;
    
    // Desconto por prazo longo
    if (prazo >= 36) taxaBase -= 0.4;
    else if (prazo >= 24) taxaBase -= 0.2;
    
    // Taxa mínima
    return Math.max(taxaBase, 1.99);
}

// Solicitar empréstimo
function solicitarEmprestimo() {
    if (!usuarioLogado) {
        alert('Você precisa estar logado para solicitar um empréstimo.');
        showLogin();
        return;
    }
    
    if (!simulacaoAtual) {
        alert('Por favor, faça uma simulação primeiro.');
        return;
    }
    
    // Enviar solicitação para o backend
    fetch('api/solicitar_emprestimo.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cliente_id: usuarioLogado.id,
            valor_solicitado: simulacaoAtual.valor,
            taxa_juros: simulacaoAtual.taxa,
            prazo_meses: simulacaoAtual.prazo,
            valor_parcela: simulacaoAtual.valorParcela,
            valor_total: simulacaoAtual.valorTotal
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Solicitação de empréstimo enviada com sucesso! Você receberá uma resposta em breve.');
            // Redirecionar para dashboard
            window.location.href = 'dashboard.html';
        } else {
            alert('Erro ao solicitar empréstimo: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao processar solicitação.');
    });
}

// Login
function handleLogin(e) {
    e.preventDefault();
    
    const email = document.getElementById('loginEmail').value;
    const senha = document.getElementById('loginPassword').value;
    
    fetch('api/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, senha })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            usuarioLogado = data.user;
            localStorage.setItem('newbank_user', JSON.stringify(usuarioLogado));
            updateUIForLoggedUser();
            closeModal('loginModal');
            alert('Login realizado com sucesso! Redirecionando para seu dashboard...');
            
            // Redirecionar automaticamente para o dashboard
            setTimeout(() => {
                window.location.href = 'dashboard.html';
            }, 1500);
        } else {
            alert('Credenciais inválidas: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao fazer login.');
    });
}

// Cadastro
function handleRegister(e) {
    e.preventDefault();
    
    const dadosCliente = {
        nome: document.getElementById('nome').value,
        email: document.getElementById('email').value,
        cpf: document.getElementById('cpf').value,
        telefone: document.getElementById('telefone').value,
        data_nascimento: document.getElementById('dataNascimento').value,
        renda_mensal: parseFloat(document.getElementById('rendaMensal').value),
        senha: document.getElementById('senha').value,
        endereco: document.getElementById('endereco').value
    };
    
    // Validações
    if (!validarCPF(dadosCliente.cpf)) {
        alert('CPF inválido!');
        return;
    }
    
    if (!validarEmail(dadosCliente.email)) {
        alert('E-mail inválido!');
        return;
    }
    
    if (dadosCliente.senha.length < 6) {
        alert('A senha deve ter pelo menos 6 caracteres!');
        return;
    }
    
    fetch('api/cadastro.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(dadosCliente)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Cadastro realizado com sucesso!');
            closeModal('registerModal');
            showLogin();
        } else {
            alert('Erro no cadastro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao realizar cadastro.');
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
    .catch(error => {
        console.error('Erro ao salvar simulação:', error);
    });
}

// Atualizar UI para usuário logado
function updateUIForLoggedUser() {
    const navMenu = document.querySelector('.nav-menu');
    const loginLink = navMenu.querySelector('a[onclick="showLogin()"]');
    const registerBtn = navMenu.querySelector('a[onclick="showRegister()"]');
    const dashboardLink = navMenu.querySelector('.dashboard-link');
    
    if (loginLink && registerBtn) {
        // Mostrar link do dashboard
        if (dashboardLink) {
            dashboardLink.style.display = 'inline-block';
        }
        
        // Atualizar link de login para mostrar nome do usuário
        loginLink.textContent = `Olá, ${usuarioLogado.nome.split(' ')[0]}`;
        loginLink.removeAttribute('onclick');
        loginLink.href = 'dashboard.html';
        loginLink.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'dashboard.html';
        });
        
        // Atualizar botão de cadastro para logout
        registerBtn.textContent = 'Sair';
        registerBtn.removeAttribute('onclick');
        registerBtn.href = '#';
        registerBtn.classList.remove('btn-primary');
        registerBtn.classList.add('btn-secondary');
        registerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            logout();
        });
    }
}

// Preencher formulário de login com conta de teste
function fillLoginForm(email, password) {
    document.getElementById('loginEmail').value = email;
    document.getElementById('loginPassword').value = password;
    
    // Adicionar efeito visual para mostrar que foi preenchido
    const emailInput = document.getElementById('loginEmail');
    const passwordInput = document.getElementById('loginPassword');
    
    emailInput.style.background = 'rgba(138, 43, 226, 0.1)';
    passwordInput.style.background = 'rgba(138, 43, 226, 0.1)';
    
    setTimeout(() => {
        emailInput.style.background = '';
        passwordInput.style.background = '';
    }, 1000);
    
    // Scroll para o formulário
    document.getElementById('loginForm').scrollIntoView({
        behavior: 'smooth',
        block: 'center'
    });
}

// Logout
function logout() {
    usuarioLogado = null;
    localStorage.removeItem('newbank_user');
    location.reload();
}

// Máscaras de input
function aplicarMascaras() {
    // Máscara CPF
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
        cpfInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        });
    }
    
    // Máscara Telefone
    const telefoneInput = document.getElementById('telefone');
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{4,5})(\d{4})$/, '$1-$2');
            e.target.value = value;
        });
    }
}

// Validações
function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g, '');
    if (cpf.length !== 11 || !!cpf.match(/(\d)\1{10}/)) return false;
    
    let soma = 0, resto;
    for (let i = 1; i <= 9; i++) soma = soma + parseInt(cpf.substring(i-1, i)) * (11 - i);
    resto = (soma * 10) % 11;
    if ((resto === 10) || (resto === 11)) resto = 0;
    if (resto !== parseInt(cpf.substring(9, 10))) return false;
    
    soma = 0;
    for (let i = 1; i <= 10; i++) soma = soma + parseInt(cpf.substring(i-1, i)) * (12 - i);
    resto = (soma * 10) % 11;
    if ((resto === 10) || (resto === 11)) resto = 0;
    if (resto !== parseInt(cpf.substring(10, 11))) return false;
    
    return true;
}

function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Formatação de moeda
function formatarMoeda(valor) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(valor);
} 