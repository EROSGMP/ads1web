<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Funcionarios</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <div class="demo-page">
    <div class="demo-page-navigation">
        <nav>
            <ul>
                <li>
                <a href="#funcionario">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tool">
                    </svg>
                    Cadastro</a>
                </li>
                <li>
                <a href="#funcionariolst">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tool">
                    </svg>
                    Relatório</a>
                </li>
                <li>
                <a href="#funcionarioup">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tool">
                    </svg>
                    Alteração</a>
                </li>
                <li>
                <a href="#funcionariodel">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tool">
                    </svg>
                    Exclusão</a>
                </li>
            </ul>
        </nav>
    </div>
    <main class="demo-page-content">
        <section>
            <form action="" method="post" name="Funcionario">
                <div class="href-target" id="funcionario"></div>
                <h1>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-align-justify">
                    <line x1="21" y1="10" x2="3" y2="10" />
                    <line x1="21" y1="6" x2="3" y2="6" />
                    <line x1="21" y1="14" x2="3" y2="14" />
                    <line x1="21" y1="18" x2="3" y2="18" />
                    </svg>
                    Cadastrar novos funcionarios
                </h1>
                
                <p></p>

                <div class="nice-form-group">
                    <label for="codigof">Codigo</label>
                    <input type="text" id="codigof" name="codigof" placeholder="1234" required/>
                </div>
                
                <div class="nice-form-group">
                    <label for="nomef">Nome</label>
                    <input type="text" id="nomef" name="nomef" placeholder="Pedro" required/>
                </div>

                <div class="nice-form-group">
                    <label for="CPF">CPF</label>
                    <input type="text" id="CPF" name="CPF" placeholder="12345678911" required/>
                </div>

                <div class="nice-form-group">
                    <label for="data_ncto"></label>
                    <input type="date" id="data_ncto" name="data_ncto" value="2004-04-12" required/>
                </div>

                <div class="nice-form-group">
                    <label for="salario"></label>
                    <input type="text" id="salario" name="salario" placeholder="1234.56" required/>
                </div>

                
                <input type="submit" name="funcionarionovo" value="submit" required/>
                



            </form>
        </section>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['botao'])) {
            include('config.php');

            // Sanitização dos dados
            $nome = mysqli_real_escape_string($mysqli, htmlspecialchars($_POST['nome']));
            $cpf = mysqli_real_escape_string($mysqli, htmlspecialchars($_POST['cpf']));
            $data_ncto = mysqli_real_escape_string($mysqli, htmlspecialchars($_POST['data_ncto']));

            // Inserção no banco de dados
            $insere = "INSERT INTO Funcionario (nome, cpf, data_ncto) VALUES ('$nome', '$cpf', '$data_ncto')";
            if (mysqli_query($mysqli, $insere)) {
                echo "<script>alert('Dados gravados com sucesso!');</script>";
            } else {
                echo "<script>alert('Erro ao gravar dados: " . mysqli_error($mysqli) . "');</script>";
            }
        }
        ?>
    </main>
    </div>
</body>
</html>

