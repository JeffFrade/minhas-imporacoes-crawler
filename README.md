# Minhas Importações Crawler
---
Esse projeto é um crawler que navega no sistema Minhas Importações da Empresa Brasileira de Correios e Telégrafos (Correios).

## Stack
---
O ambiente foi criado em containers (Com o `Docker`).
Foi utilizada a seguinte stack para o projeto:

- `Laravel >= 8`
- `PHP >= 7.4`
- `MySQL >= 5`

## Como Configurar:
---
Para configurar o projeto, basta executar o script `config.sh`, que todo o ambiente será montado.
Também é necessário preencher algumas variáveis no `.env` após executar o `config.sh`.
Sendo as seguintes variáveis:
- `MAIL_MAILER` = SMTP/POP (Depende do protocolo que prefere utilizar).
- `MAIL_HOST`= Host do SMTP/POP que pretende utilizar.
- `MAIL_PORT` = Porta do SMTP/POP desejado.
- `MAIL_USERNAME` = E-mail que fará os disparos.
- `MAIL_PASSWORD` = Senha do e-mail que fará os disparos.
- `MAIL_ENCRYPTION` = Criptografia do e-mail (Geralmente utiliza-se TLS mesmo, não há necessidade de alterar muitas vezes).
- `MAIL_FROM_ADDRESS` = Qual o e-mail que receberá os alertas.
- `MINHAS_IMPORTACOES_USERNAME` = Usuário que utiliza para logar no sistema do Minhas Importações.
- `MINHAS_IMPORTACOES_PASSWORD` = Senha que utiliza para logar no sistema do Minhas Importações.

Vale ressaltar que nenhuma dessas informações será jogada na WEB, apenas são necessárias para o crawler conseguir trabalhar.

E também adicionar a seguinte linha ao `crontab` (Caso queira que o robô rode de forma automática a cada intervalo de tempo):
- `* * * * * docker exec minhas-importacoes-crawler-php-fpm php artisan schedule:run >> /dev/null 2>&1`

Para acessar o `crontab` em ambientes `Linux`, basta digitar `crontab -e` e colar a linha acima.

## Como executar o Crawler
---

Uma vez que tudo esteja configurado, basta executar o script `run-crawler.sh` que o robô executará.
