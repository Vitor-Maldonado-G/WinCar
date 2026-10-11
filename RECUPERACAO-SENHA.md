# Recuperacao de senha

## Configurar o Gmail

1. Ative a verificacao em duas etapas em projetowincar@gmail.com.
2. Abra https://myaccount.google.com/apppasswords e crie uma senha de app chamada WinCar.
3. Copie config/email.example.php para config/email.local.php, caso ainda nao exista.
4. Preencha senha_app apenas no arquivo local. Nao envie essa senha no chat nem pelo Git. Nao use a senha normal da conta.
5. Confira url_base: http://localhost/WinCar funciona quando o link e aberto no mesmo computador do XAMPP. Para outro computador, configure o endereco acessivel do site; em hospedagem, use HTTPS.
6. Em instalacoes existentes, execute database/recuperacao-senha.sql no banco wincar. Esse arquivo nao apaga dados. Nao reimporte o dump completo para atualizar um banco existente.

## Testar

Com Apache e MySQL ligados, abra login.php, escolha Esqueci minha senha e informe o e-mail de uma conta cadastrada no site. Abra o e-mail recebido, defina a senha e entre novamente. Teste tambem reutilizar o link: ele deve ser recusado.

O Gmail remetente nao precisa ser uma conta de cliente. Para receber um teste nele, cadastre esse endereco no site.

Teste automatizado local: `C:\xampp\php\php.exe tests/recuperacao-senha.php`. Usa tabelas temporarias e nao altera contas existentes. Requer o MySQL local do XAMPP, banco wincar e usuario root sem senha, como no projeto.

## Funcionamento

- recuperar-senha.php valida o formulario e solicita o envio.
- includes/enviar-email.php envia pelo Gmail com PHPMailer 7.1.1 e TLS.
- includes/recuperacao-senha.php concentra validacao, criacao e consumo dos links.
- redefinir-senha.php recebe o link e grava a nova senha com password_hash.
- O banco guarda apenas o hash do token. O link dura 30 minutos e pode ser usado uma vez. Uma nova solicitacao substitui o link anterior; ha intervalo de um minuto por conta e por sessao.
- A resposta nao informa se um e-mail esta cadastrado. Erros de envio sao registrados no log do PHP, sem senha ou token.
- A troca nao encerra sessoes ja abertas em outros navegadores. Para publicacao real, considerar invalidacao dessas sessoes e limites adicionais de solicitacoes por IP.
- Se mudar a senha da conta Google, pode ser necessario gerar outra senha de app.

Referencia: https://support.google.com/accounts/answer/185833?hl=pt-BR
