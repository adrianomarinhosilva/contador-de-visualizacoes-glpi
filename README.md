Plugin de Monitoramento de Visualizações de Tickets no GLPI
Visão Geral do Projeto
O plugin "Ticket Views" é uma extensão para o sistema GLPI que permite rastrear e exibir quem visualizou um determinado ticket, oferecendo transparência e colaboração na gestão de chamados.
Características Principais
Funcionalidades de Rastreamento

Registro de Visualizações

Monitora quem visualizou cada ticket
Registra data e hora da visualização
Identifica usuários únicos


Interface de Visualização

Aba dedicada para mostrar visualizadores
Contador de visualizações
Detalhes do criador do ticket


**Atualização Dinâmica

Contador atualizado em tempo real
Intervalo de atualização configurável
Integração com a interface do GLPI



Componentes Técnicos
Banco de Dados

Tabela glpi_plugin_ticketviews_viewers
Campos:

tickets_id: Identificador do ticket
users_id: Identificador do usuário
view_date: Momento da visualização



Scripts Principais
Backend (PHP)

get_viewers_count.php: Recupera total de visualizadores
get_viewers_list.php: Lista detalhada de visualizadores
register_view.php: Registra novas visualizações
ticket.class.php: Lógica de processamento de visualizações
viewer.class.php: Métodos auxiliares de gerenciamento

Frontend (JavaScript)

Atualização dinâmica do contador
Requisições AJAX para buscar informações
Integração com elementos da interface GLPI

Fluxo de Funcionamento

Usuário acessa um ticket
Sistema registra automaticamente a visualização
Atualiza o contador de visualizações
Permite visualizar lista de usuários que viram o ticket

Recursos de Segurança

Verificação de permissão de acesso
Autenticação de usuário
Proteção contra acessos não autorizados

Benefícios

Transparência na visualização de tickets
Rastreamento de interações
Melhoria na comunicação da equipe
Insights sobre engajamento dos tickets

Possíveis Melhorias

Exportação de relatórios de visualização
Filtros avançados
Configurações personalizáveis
Estatísticas de visualização

Tecnologias

Linguagens: PHP, JavaScript
Biblioteca: jQuery
Integração: GLPI
Banco de Dados: MySQL

Considerações Finais
O plugin oferece uma solução simples e eficiente para monitorar a visualização de tickets, aumentando a transparência e colaboração em ambientes de suporte e gestão de chamados.
