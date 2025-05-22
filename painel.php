<?php
include ('dbConfig.php');
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel - NewBank</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>NewBank</h1>
        <p>Bem-vindo ao seu painel, cliente VIP.</p>
    </header>

    <nav>
        <a href="emprestimo.php">💸 Empréstimo</a>
        <a href="config.php">⚙️ Configurações</a>
        <a href="logout.php">🚪 Sair</a>
    </nav>

    <main>
        <div class="box">
            <h2>Olá, <?php echo htmlspecialchars($_SESSION['usuario']); ?>!</h2>
            <p>Saldo disponível:</p>
            <div id="valor-saldo" class="saldo">R$ 1.000.000,00</div>
            <button class="toggle-btn" onclick="toggleSaldo()">Esconder saldo</button>
            <p style="margin-top: 25px;">Gerencie seus empréstimos com segurança e praticidade.</p>
        </div>
    </main>

    <footer>
        &copy; <?php echo date('Y'); ?> NewBank. Todos os direitos reservados.
    </footer>

    <script>
        let saldoVisivel = true;
        function toggleSaldo() {
            const saldo = document.getElementById("valor-saldo");
            const botao = document.querySelector(".toggle-btn");
            if (saldoVisivel) {
                saldo.textContent = "••••••••••";
                botao.textContent = "Mostrar saldo";
            } else {
                saldo.textContent = "R$ 1.000.000,00";
                botao.textContent = "Esconder saldo";
            }
            saldoVisivel = !saldoVisivel;
        }
    </script>
</body>
</html>

