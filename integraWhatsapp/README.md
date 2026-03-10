# Módulo Integra WhatsApp

Este módulo integra o WhatsApp Business ao Magento 2, permitindo gerenciar configurações de API e números.

## Requisitos

- Magento 2.4.x
- PHP 7.4+

## Instalação

### Opção 1: Path Repository (Recomendada)

Como este módulo está localizado em um subdiretório (`integraWhatsapp`) de um repositório maior (`magento2_modulos`), a instalação via **Path Repository** é a mais simples e funciona tanto para repositórios privados quanto públicos.

1.  **Clone o repositório** em uma pasta auxiliar (ex: `packages/`):
    ```bash
    mkdir packages
    cd packages
    git clone https://github.com/danielwebart/magento2_modulos.git .
    ```

2.  **Configure o `composer.json`** do seu projeto Magento:
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

3.  **Instale o módulo**:
    ```bash
    composer require integra/module-whatsapp
    ```

### Opção 2: Repositório VCS (Se o módulo estivesse na raiz)

Se este módulo estivesse na **raiz** de um repositório (público ou privado), você não precisaria clonar manualmente. Bastaria adicionar o repositório ao `composer.json`:

```json
"repositories": {
    "integra-whatsapp": {
        "type": "vcs",
        "url": "https://github.com/danielwebart/magento2_modulos.git"
    }
}
```

**Nota:** Como o arquivo `composer.json` deste módulo **não está na raiz** do repositório Git, o Composer não consegue encontrá-lo automaticamente usando o tipo `vcs`. Por isso, a **Opção 1** é necessária neste caso específico de "monorepo".

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
