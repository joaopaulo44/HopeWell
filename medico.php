<?php
$host = "localhost";
$db = "paciente_triagens";
$user = "root";
$pass = "";

// 1. Conecta ao banco de dados
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// 2. Busca todos os pacientes que estão com status 'Aguardando'
$sql = "SELECT * FROM triagens WHERE status = 'Aguardando' ORDER BY data_envio ASC";
$resultado_pacientes = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>HopeWell - Painel Médico</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body { background-color: #f8f9fa; }
    .logo { max-width: 130px; }
    header { padding: 1.5rem 0; }
    th { width: 35%; background-color: #f1f3f5 !important; }
    /* Esconde a área de resposta inicialmente */
    #area-resposta { display: none; }
  </style>
</head>
<body>

<?php 
// Verifica se na URL possui o parâmetro ?status=sucesso enviado pelo processa_resposta.php
if (isset($_GET['status']) && $_GET['status'] == 'sucesso') {
    echo '<div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show shadow-sm text-center fw-bold" role="alert">
                Atendimento e diagnóstico salvos com sucesso no banco de dados!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          </div>';
}
?>

  <header class="bg-white shadow-sm text-center">
    <img src="imagens/logo.png" alt="Logo HopeWell" class="logo">
    <h1 class="mt-2 h3 text-secondary">HopeWell Medical Center</h1>
  </header>

  <nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-center border-bottom shadow-sm py-2">
    <div class="navbar-nav">     
      <a class="nav-link btn btn-outline-success mx-2 px-3" href="index.html">Triagem do Paciente</a>
      <a class="nav-link btn btn-outline-primary mx-2 px-3 active text-white bg-primary" href="medico.php">Painel do Médico</a>
      <a class="nav-link btn btn-outline-primary mx-2 px-3" href="resultado.php">resultado</a>
    </div>
  </nav>
  
  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        
        <div class="card shadow border-0 rounded-3 mb-4">
          <div class="card-header bg-primary text-white text-center py-3">
            <h4 class="mb-0">Área do Médico - Avaliação de Triagens</h4>
          </div>
          <div class="card-body p-4">
            
            <div class="mb-4">
              <label for="pacienteSelect" class="form-label fw-bold text-secondary">Selecione um paciente na fila:</label>
              
              <select id="pacienteSelect" class="form-select form-select-lg shadow-sm">
                <option value="">-- Selecione um paciente --</option>
                <?php 
                // 3. O PHP cria uma tag <option> para cada paciente encontrado
                if ($resultado_pacientes->num_rows > 0) {
                    while($row = $resultado_pacientes->fetch_assoc()) {
                        // Guardamos os dados nos atributos "data-" para o JavaScript usar depois
                        echo "<option value='{$row['id']}' 
                                data-nome='{$row['nome']}' 
                                data-idade='{$row['idade']}' 
                                data-pressao='{$row['pressao']}' 
                                data-sintomas='{$row['sintomas']}' 
                                data-comorbidade='{$row['comorbidade']}' 
                                data-alergia='{$row['alergia']}' 
                                data-diabetes='{$row['diabetes']}' 
                                data-data='{$row['data_envio']}'>
                                ID: {$row['id']} - {$row['nome']}
                              </option>";
                    }
                } else {
                    echo "<option value=''>Nenhum paciente aguardando.</option>";
                }
                ?>
              </select>

            </div>

            <div class="table-responsive">
              <table id="tabelaPaciente" class="table table-bordered table-striped align-middle shadow-sm mb-0">
                <tbody>
                  <tr><th>ID</th><td id="td_id" class="fw-bold text-muted">-</td></tr> 
                  <tr><th>Nome</th><td id="td_nome">-</td></tr>
                  <tr><th>Idade</th><td id="td_idade">-</td></tr>
                  <tr><th>Pressão</th><td id="td_pressao">-</td></tr>
                  <tr><th>Sintomas</th><td id="td_sintomas">-</td></tr>
                  <tr><th>Comorbidade</th><td id="td_comorbidade">-</td></tr>
                  <tr><th>Alergia</th><td id="td_alergia">-</td></tr>
                  <tr><th>Diabetes</th><td id="td_diabetes">-</td></tr>
                  <tr><th>Data de Envio</th><td id="td_data" class="text-muted">-</td></tr>
                </tbody>
              </table>
            </div>

          </div>
        </div>

        <div class="card shadow border-0 rounded-3" id="area-resposta">
          <div class="card-header bg-dark text-white py-2">
            <h5 class="mb-0"><i class="bi bi-file-medical"></i> Prontuário / Resposta do Médico</h5>
          </div>
          <div class="card-body p-4">
            <form id="form-resposta" method="POST" action="processa_resposta.php">
              <input type="hidden" name="id_paciente" id="id_paciente_form">
              
              <div class="mb-3">
                <label for="resposta_medico" class="form-label fw-bold">Diagnóstico, Prescrição ou Observações:</label>
                <textarea class="form-control" name="resposta_medico" id="resposta_medico" rows="5" placeholder="Digite aqui o atendimento..." required></textarea>
              </div>
              
              <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-4 shadow-sm">Salvar Atendimento</button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    document.getElementById('pacienteSelect').addEventListener('change', function() {
        // Pega a opção que foi selecionada
        let selecionado = this.options[this.selectedIndex];
        let areaResposta = document.getElementById('area-resposta');
        let inputIdOculto = document.getElementById('id_paciente_form');

        // Se selecionou um paciente válido (value não está vazio)
        if(this.value !== "") {
            // Puxa os dados armazenados nos atributos "data-"
            document.getElementById('td_id').textContent = this.value;
            document.getElementById('td_nome').textContent = selecionado.getAttribute('data-nome');
            document.getElementById('td_idade').textContent = selecionado.getAttribute('data-idade');
            document.getElementById('td_pressao').textContent = selecionado.getAttribute('data-pressao');
            document.getElementById('td_sintomas').textContent = selecionado.getAttribute('data-sintomas');
            document.getElementById('td_comorbidade').textContent = selecionado.getAttribute('data-comorbidade') === "1" ? "Sim" : "Não";
            document.getElementById('td_alergia').textContent = selecionado.getAttribute('data-alergia') === "1" ? "Sim" : "Não";
            document.getElementById('td_diabetes').textContent = selecionado.getAttribute('data-diabetes') === "1" ? "Sim" : "Não";
            document.getElementById('td_data').textContent = selecionado.getAttribute('data-data');

            // Mostra a área de resposta do médico e salva o ID no formulário
            areaResposta.style.display = 'block';
            inputIdOculto.value = this.value;
        } else {
            // Se voltou para "-- Selecione --", limpa tudo
            let colunas = document.querySelectorAll('#tabelaPaciente td');
            colunas.forEach(td => td.textContent = '-');
            
            areaResposta.style.display = 'none';
            inputIdOculto.value = "";
        }
    });
  </script>
</body>
</html>
<?php $conn->close(); ?>