<?php
// processa_triagens.php
$host = "localhost";
$db = "paciente_triagens";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $idade = $_POST['idade'];
    $pressao = $_POST['pressao'];
    $sintomas = $_POST['sintomas'];
    
    // Checkboxes retornam 'on' se marcados, senão não são enviados
    $comorbidade = isset($_POST['comorbidade']) ? 1 : 0;
    $alergia = isset($_POST['alergia']) ? 1 : 0;
    $diabetes = isset($_POST['diabetes']) ? 1 : 0;

    $sql = "INSERT INTO triagens (nome, idade, pressao, sintomas, comorbidade, alergia, diabetes) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissiii", $nome, $idade, $pressao, $sintomas, $comorbidade, $alergia, $diabetes);
    
    if ($stmt->execute()) {
        header("Location: resultado.php"); // Redireciona para a tela de sucesso
        exit();
    } else {
        echo "Erro ao salvar triagem: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>