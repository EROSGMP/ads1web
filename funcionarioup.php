<html>
<head>
    <title>Alteração de Funcionarios</title>
    <?php include('config.php'); ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="./style.css">
</head>

<body>
    <form action="funcionarioup.php" method="post" name="form1">
        <table>
            <tr>
                <td class="tabletitle">Alteração de Funcionarios</td>
            </tr>
            <tr>
                <td class="tablename">Matrícula:</td>
                <td class="tableanswer">
                    <input type="number" name="codigof" id="codigof" required 
                           onchange="buscarFuncionario()" 
                           value="<?php echo isset($_GET['codigof']) ? htmlspecialchars($_GET['codigof']) : ''; ?>" />
                </td>
                <td class="tablename">nomef:</td>
                <td class="tableanswer">
                    <input type="text" name="nomef" id="nomef" 
                           value="<?php echo isset($Funcionario['nomef']) ? htmlspecialchars($Funcionario['nomef']) : ''; ?>" />
                </td>
            </tr>
            <tr>
                <td class="tablename">CPF:</td>
                <td class="tableanswer">
                    <input type="text" name="cpf" id="cpf" 
                           value="<?php echo isset($Funcionario['cpf']) ? htmlspecialchars($Funcionario['cpf']) : ''; ?>" />
                </td>
                <td class="tablename">Data de Nascimento:</td>
                <td class="tableanswer">
                    <input type="date" name="data_ncto" id="data_ncto" 
                           value="<?php echo isset($Funcionario['data_ncto']) ? htmlspecialchars($Funcionario['data_ncto']) : ''; ?>" />
                </td>
            </tr>
            <tr>
                <td class="tabletitle">
                    <input type="submit" name="botao" value="Alterar" />
                    <input type="button" value="Cancelar" onclick="window.location.href='index.html'" />
                </td>
            </tr>
        </table>
    </form>

    <script>
    function buscarFuncionario() {
        var codigof = $('#codigof').val();
        
        if(codigof > 0) {
            $.ajax({
                url: 'busca_Funcionario.php',
                type: 'GET',
                data: { codigof: codigof },
                dataType: 'json',
                success: function(data) {
                    if(data.error) {
                        alert(data.error);
                    } else {
                        $('#nomef').val(data.nomef || '');
                        $('#cpf').val(data.cpf || '');
                        $('#data_ncto').val(data.data_ncto || '');
                    }
                },
                error: function() {
                    alert('Erro ao buscar Funcionario');
                }
            });
        }
    }
    </script>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['botao']) && $_POST['botao'] == "Alterar") {
        // Recebe os dados do formulário
        $codigof = intval($_POST['codigof']);
        $nomef = mysqli_real_escape_string($mysqli, $_POST['nomef']);
        $cpf = mysqli_real_escape_string($mysqli, $_POST['cpf']);
        $data_ncto = mysqli_real_escape_string($mysqli, $_POST['data_ncto']);

        // Validação básica da matrícula
        if ($codigof > 0) {
            // Monta a query de atualização dinamicamente
            $updates = [];
            if (!empty($nomef)) {
                $updates[] = "nomef = '$nomef'";
            }
            if (!empty($cpf)) {
                $updates[] = "cpf = '$cpf'";
            }
            if (!empty($data_ncto)) {
                $updates[] = "data_ncto = '$data_ncto'";
            }

            // Se houver campos para atualizar
            if (!empty($updates)) {
                $query = "UPDATE Funcionario SET " . implode(", ", $updates) . " WHERE codigof = '$codigof'";
                if (mysqli_query($mysqli, $query)) {
                    echo "Registro atualizado com sucesso!";
                } else {
                    echo "Erro ao atualizar o registro: " . mysqli_error($mysqli);
                }
            } else {
                echo "Nenhum campo foi fornecido para atualização.";
            }
        } else {
            echo "Matrícula inválida.";
        }

        // Fecha a conexão com o banco de dados
        mysqli_close($mysqli);
     }
    ?>

    <br />
    <a href="index.html">Home</a>
</body>
</html>