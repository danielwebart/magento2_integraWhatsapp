# Módulo Integra WhatsApp

Este módulo integra o WhatsApp Business ao Magento 2, permitindo gerenciar configurações de API e números.

## Requisitos

- Magento 2.4.x
- PHP 7.4+

## Instalação via Composer (Path Repository)

Como este módulo está em um repositório privado ou monorepo (`magento2_modulos`), a instalação recomendada é utilizando o repositório local (`path`).

### Passo 1: Clonar o Repositório de Módulos

Clone o repositório `magento2_modulos` em uma pasta acessível ao seu projeto Magento (por exemplo, na raiz, ao lado da pasta `vendor` ou em `packages/`).

```bash
cd /caminho/para/seu/projeto/magento
mkdir packages
cd packages
git clone https://github.com/danielwebart/magento2_modulos.git .
```

A estrutura final deve ficar parecida com:
```
magento_root/
├── app/
├── vendor/
├── composer.json
└── packages/
    └── integraWhatsapp/
        ├── composer.json
        ├── registration.php
        └── ...
```

### Passo 2: Configurar o `composer.json` do Projeto Magento

Edite o arquivo `composer.json` na raiz do seu projeto Magento e adicione o repositório do tipo `path`:

```json
"repositories": {
    "integra-whatsapp": {
        "type": "path",
        "url": "packages/integraWhatsapp",
        "options": {
            "symlink": true
        }
    }
}
```
*Nota: Se você já tem outros repositórios configurados, adicione este à lista.*

### Passo 3: Requerer o Módulo

Execute o comando `composer require` para instalar o módulo:

```bash
composer require integra/module-whatsapp
```

### Passo 4: Habilitar o Módulo

Após a instalação, habilite o módulo no Magento:

```bash
php bin/magento module:enable Integra_Whatsapp
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
```

## Configuração

Acesse o painel administrativo do Magento e navegue até **WhatsApp Business Integration > Configuração Número/API**.
