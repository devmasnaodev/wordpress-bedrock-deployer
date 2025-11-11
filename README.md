# WordPress Bedrock — Projeto de Estudo com Pipeline de Deploy

Este repositório é um projeto de estudo baseado em Bedrock (Roots) criado para demonstrar um fluxo de desenvolvimento com Composer e automação de deploy usando GitHub Actions + Deployer.

A imagem do runner foi personalizada com base no exemplo https://github.com/devmasnaodev/gh-actions-runner. Ela incorpora os serviços e dependências necessários, otimizando o processo de deploy.

## Principais funcionalidades

- Estrutura Bedrock pronta para desenvolvimento
- Gerenciamento de dependências via `composer.json`
- Configuração por ambiente com arquivos `.env`
- Pipeline de CI/CD (GitHub Actions) que aciona Deployer para automação do deploy


## Instalação com DDEV

Você pode levantar este projeto localmente com DDEV apenas importando o repositório, seguindo o fluxo do guia oficial do DDEV para WordPress (git clone). 
- https://docs.ddev.com/en/stable/users/quickstart/#wordpress-git-clone


## Ajustes e personalização

- Atualize o arquivo de workflow do GitHub Actions para refletir seus hosts/ambientes.
- Customize o `deploy.php` (Deployer) para as suas tasks (sincronização de uploads, migrations, cache, etc.).

## Observações

- Este repositório é destinado ao aprendizado e como ponto de partida. Revise as configurações de segurança (chaves SSH, permissões, variáveis sensíveis) antes de usar em produção.
- Verifique as versões em `composer.json` e atualize conforme necessário para o seu ambiente.

## Referências

- Bedrock: https://roots.io/bedrock/
- Deployer: https://deployer.org/
- GitHub Actions: https://docs.github.com/actions
