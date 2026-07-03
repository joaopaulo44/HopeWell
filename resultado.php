<?php
$host = "localhost";
$db = "paciente_triagens";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$resultados = null;
$buscou = false;

// Se o usuário digitou um nome e clicou em buscar
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca_nome'])) {
    $buscou = true;
    $nome_busca = $_POST['busca_nome'];
    
    // Busca no banco os dados do paciente parecido com o nome digitado, do mais recente para o mais antigo
    $sql = "SELECT * FROM triagens WHERE nome LIKE ? ORDER BY data_envio DESC";
    $stmt = $conn->prepare($sql);
    
    // O % permite achar nomes incompletos (ex: se digitar 'João', acha 'João Silva')
    $termo = "%" . $nome_busca . "%"; 
    $stmt->bind_param("s", $termo);
    $stmt->execute();
    $resultados = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>HopeWell Medical Center - Resultado do Paciente</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .logo { max-width: 130px; }
    header { padding: 1.5rem 0; }
  </style>
</head>
<body>

  <header class="bg-white shadow-sm text-center">
    <img src="imagens/logo.png" alt="Logo HopeWell" class="logo">
    <h1 class="mt-2 h3 text-secondary">HopeWell Medical Center</h1>
  </header>

  <nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-center border-bottom shadow-sm py-2">
    <div class="navbar-nav">      
      <a class="nav-link btn btn-outline-success mx-2 px-3 active text-white bg-success" href="index.html">Triagem do Paciente</a>
      <a class="nav-link btn btn-outline-primary mx-2 px-3" href="medico.php">Painel do Médico</a>
      <a class="nav-link btn btn-outline-primary mx-2 px-3" href="resultado.php">resultado</a>
    </div>
  </nav>

  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
          
        <?php if (isset($_GET['status']) && $_GET['status'] == 'sucesso'): ?>
            <div class="alert alert-success text-center mb-4 shadow-sm">
                <strong>Triagem enviada com sucesso!</strong> Aguarde o atendimento e use a busca abaixo para ver seu diagnóstico.
            </div>
        <?php endif; ?>

        <div class="card shadow border-0 rounded-3 text-center mb-4">
          <div class="card-header bg-success text-white py-3">
            <h4 class="mb-0">Consultar Diagnóstico</h4>
          </div>
          <div class="card-body p-4">
            <form method="POST" action="resultado.php" class="d-flex justify-content-center align-items-center gap-2">
                <input type="text" name="busca_nome" class="form-control form-control-lg" placeholder="Digite seu nome completo..." required>
                <button type="submit" class="btn btn-success btn-lg px-4">Buscar</button>
            </form>
          </div>
        </div>

        <?php if ($buscou): ?>
            <?php if ($resultados && $resultados->num_rows > 0): ?>
                
                <h4 class="text-secondary mb-3">Resultados encontrados:</h4>
                
                <?php while($row = $resultados->fetch_assoc()): ?>
                    <div class="card shadow-sm border-0 mb-4 <?php echo ($row['status'] == 'Atendido') ? 'border-start border-5 border-success' : 'border-start border-5 border-warning'; ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title text-primary mb-0"><?php echo htmlspecialchars($row['nome']); ?></h5>
                                <?php if ($row['status'] == 'Atendido'): ?>
                                    <span class="badge bg-success fs-6">Atendido</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark fs-6">Aguardando Atendimento</span>
                                <?php endif; ?>
                            </div>
                            
                            <p class="text-muted small">Enviado em: <?php echo date("d/m/Y H:i", strtotime($row['data_envio'])); ?></p>
                            
                            <hr>
                            
                            <h6 class="fw-bold mt-3">Dados da Triagem:</h6>
                            <ul class="list-unstyled">
                                <li><strong>Idade:</strong> <?php echo $row['idade']; ?> anos</li>
                                <li><strong>Pressão:</strong> <?php echo htmlspecialchars($row['pressao']); ?></li>
                                <li><strong>Sintomas relatados:</strong> <?php echo htmlspecialchars($row['sintomas']); ?></li>
                            </ul>

                            <hr>
                            
                            <h6 class="fw-bold text-success mt-3">Resposta do Médico:</h6>
                            <?php if (!empty($row['resposta_medico'])): ?>
                                <div class="p-3 bg-light rounded border">
                                    <?php echo nl2br(htmlspecialchars($row['resposta_medico'])); ?>
                                </div>
                            <?php else: ?>
                                <div class="p-3 bg-light rounded border text-muted fst-italic">
                                    O médico ainda não registrou o diagnóstico. Por favor, aguarde e consulte novamente mais tarde.
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endwhile; ?>

            <?php else: ?>
                <div class="alert alert-danger text-center">
                    Nenhum paciente encontrado com o nome <strong>"<?php echo htmlspecialchars($nome_busca); ?>"</strong>. Verifique se digitou corretamente.
                </div>
            <?php endif; ?>
        <?php endif; ?>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>