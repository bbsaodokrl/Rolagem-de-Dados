function rolar() {
    var dado = document.getElementById('dado').value;

    if (!dado) {
        alert("Por favor, insira o valor do dado no formato '1d20'.");
        return;
    }

    fetch(`rolar.php?dado=${dado}`)
        .then(response => response.json())
        .then(data => {
            var resultadoDiv = document.getElementById('resultado');
            if (data.resultado) {
                resultadoDiv.innerHTML = `Resultado: ${data.resultado.join(', ')}`;
            } else {
                resultadoDiv.innerHTML = "Erro ao rolar os dados.";
            }
        })
        .catch(error => console.error("Erro na requisição:", error));
}

function mostrarUsuario() {
    const urlParams = new URLSearchParams(window.location.search);
    const code = urlParams.get('code');

    if (code) {
        fetch('discord-callback.php?code=' + code)
            .then(response => response.json())
            .then(data => {
                const discordInfoDiv = document.getElementById('discord-info');
                if (data.username) {
                    discordInfoDiv.innerHTML = `
                        <div class="discord-info">
                            <img src="https://cdn.discordapp.com/avatars/${data.id}/${data.avatar}.png" alt="Avatar" class="discord-avatar">
                            <p>Bem-vindo, ${data.username}!</p>
                        </div>
                    `;
                }
            })
            .catch(error => console.error('Erro ao buscar dados do usuário:', error));
    }
}

document.addEventListener('DOMContentLoaded', mostrarUsuario);
