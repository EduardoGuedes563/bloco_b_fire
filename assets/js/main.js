// Auto-avança entre os campos do código 2FA
document.querySelectorAll('.codigo-2fa input').forEach((input, idx, inputs) => {
    input.addEventListener('input', (e) => {
        // Permite apenas números
        e.target.value = e.target.value.replace(/\D/g, '');

        if (e.target.value && idx < inputs.length - 1) {
            inputs[idx + 1].focus();
        }
        // Submete automaticamente ao preencher o último campo
        if (idx === inputs.length - 1 && e.target.value) {
            document.getElementById('form2fa')?.submit();
        }
    });

    input.addEventListener('keydown', (e) => {
        // Volta ao campo anterior com backspace
        if (e.key === 'Backspace' && !e.target.value && idx > 0) {
            inputs[idx - 1].focus();
        }
    });

    // Ao colar o código completo no primeiro campo
    input.addEventListener('paste', (e) => {
        e.preventDefault();
        const texto = e.clipboardData.getData('text').replace(/\D/g, '');
        [...texto].slice(0, 6).forEach((char, i) => {
            if (inputs[i]) inputs[i].value = char;
        });
        inputs[Math.min(texto.length, 5)].focus();
    });
});