
        // Carrega pacientes
        fetch('get_pacientes.php')
            .then(res => res.json())
            .then(pacientes => {
                const select = document.getElementById('pacienteSelect');
                select.innerHTML = '<option value="">-- Selecione um paciente --</option>';
                pacientes.forEach(p => {
                    const opt = document.createElement('option');
                    opt.value = p.id;
                    opt.textContent = p.nome;
                    select.appendChild(opt);
                });
            });

        document.getElementById('pacienteSelect').addEventListener('change', function () {
            const id = this.value;
            if (!id) return document.getElementById('tabelaPaciente').style.display = 'none';

            fetch('get_dados_paciente.php?id=' + id)
                .then(res => res.json())
                .then(d => {
                    document.getElementById('td_id').textContent = d.id || '-';
                    document.getElementById('td_nome').textContent = d.nome || '-';
                    document.getElementById('td_idade').textContent = d.idade || '-';
                    document.getElementById('td_pressao').textContent = d.pressao || '-';
                    document.getElementById('td_sintomas').textContent = d.sintomas || '-';
                    document.getElementById('td_comorbidade').textContent = d.comorbidade == 1 ? 'Sim' : 'Não';
                    document.getElementById('td_alergia').textContent = d.alergia == 1 ? 'Sim' : 'Não';
                    document.getElementById('td_diabetes').textContent = d.diabetes == 1 ? 'Sim' : 'Não';
                    document.getElementById('td_data').textContent = d.data_envio || '-';

                    document.getElementById('tabelaPaciente').style.display = 'table';
                });
        });
 