
let listaPacientes = [];

// Quando a página carrega, busca os pacientes no banco
document.addEventListener("DOMContentLoaded", function() {
    fetch('busca_pacientes.php')
        .then(response => response.json())
        .then(dados => {
            listaPacientes = dados;
            preencherSelect(dados);
        })
        .catch(erro => console.error("Erro ao buscar pacientes:", erro));
});

function preencherSelect(pacientes) {
    let select = document.getElementById('pacienteSelect');
    select.innerHTML = '<option value="">-- Selecione um paciente --</option>';
    
    pacientes.forEach(paciente => {
        let option = document.createElement('option');
        option.value = paciente.id;
        option.textContent = `ID: ${paciente.id} - ${paciente.nome}`;
        select.appendChild(option);
    });
}


document.getElementById('pacienteSelect').addEventListener('change', function() {
    let idSelecionado = this.value;
    let areaResposta = document.getElementById('area-resposta');
    let inputIdOculto = document.getElementById('id_paciente_form');
    
    if (idSelecionado !== "") {
        // Encontra os dados do paciente selecionado
        let paciente = listaPacientes.find(p => p.id == idSelecionado);
        
        // Preenche a tabela na tela
        document.getElementById('td_id').textContent = paciente.id;
        document.getElementById('td_nome').textContent = paciente.nome;
        document.getElementById('td_idade').textContent = paciente.idade;
        document.getElementById('td_pressao').textContent = paciente.pressao;
        document.getElementById('td_sintomas').textContent = paciente.sintomas;
        document.getElementById('td_comorbidade').textContent = paciente.comorbidade == 1 ? "Sim" : "Não";
        document.getElementById('td_alergia').textContent = paciente.alergia == 1 ? "Sim" : "Não";
        document.getElementById('td_diabetes').textContent = paciente.diabetes == 1 ? "Sim" : "Não";
        document.getElementById('td_data').textContent = paciente.data_envio;

     
        areaResposta.style.display = 'block';
        inputIdOculto.value = paciente.id;
    } else {
        
        document.querySelectorAll('#tabelaPaciente td').forEach(td => td.textContent = '-');
        areaResposta.style.display = 'none';
        inputIdOculto.value = "";
    }
});