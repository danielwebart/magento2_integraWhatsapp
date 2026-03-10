# Módulo Integra WhatsApp

Este módulo integra o WhatsApp Business ao Magento 2, permitindo gerenciar configurações de API e números.

## Requisitos

- Magento 2.4.x
- PHP 7.4+

## Instalação via Composer

Adicione o repositório ao seu arquivo `composer.json`:

```json
"repositories": {
    "integra-whatsapp": {
        "type": "vcs",
        "url": "https://github.com/danielwebart/magento2_integraWhatsapp.git"
    }
}
```

Em seguida, instale o módulo:

```bash
composer require integra/module-whatsapp
```

## Habilitar o Módulo

Após a instalação:

```bash
php bin/magento module:enable Integra_Whatsapp
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
```

## Configuração

Acesse o painel administrativo do Magento e navegue até **WhatsApp Business Integration > Configuração Número/API**.
