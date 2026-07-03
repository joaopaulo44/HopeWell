<?php
// processa_resposta.php
$host = "localhost";
$db = "paciente_triagens";
$user = "root";
$pass = "";

// 1. Conecta ao banco de dados MySQL do XAMPP
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// 2. Verifica se os dados foram enviados via método POST pelo formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Captura o ID do paciente (campo hidden) e o texto do diagnóstico
    $id_paciente = $_POST['id_paciente'];
    $resposta = $_POST['resposta_medico'];

    // Validação simples para garantir que os campos não estão vazios
    if (!empty($id_paciente) && !empty($resposta)) {
        
        // 3. Prepara a query SQL para atualizar a tabela
        // Grava o texto na coluna 'resposta_medico' e muda o status para 'Atendido'
        $sql = "UPDATE triagens SET resposta_medico = ?, status = 'Atendido' WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $resposta, $id_paciente);
        
        // 4. Executa a atualização
        if ($stmt->execute()) {
            // Se der certo, redireciona de volta para a página do médico com um aviso de sucesso
            header("Location: medico.php?status=sucesso");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Erro ao salvar atendimento no banco de dados: " . $conn->error . "</div>";
        }
        
        $stmt->close();
    } else {
        echo "<div class='alert alert-warning'>Por favor, preencha o diagnóstico antes de salvar.</div>";
    }
}

$conn->close();
?>