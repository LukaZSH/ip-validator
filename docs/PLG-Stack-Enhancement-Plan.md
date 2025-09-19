# 🔍 Plano de Melhoria do Stack PLG (Promtail-Loki-Grafana)

Sistema de Validação de Presença UNESPAR - Apucarana

## 📊 Estado Atual do Projeto

### ✅ Implementações Concluídas

#### **Fase 1: Configuração Básica**
- ✅ Stack PLG (Promtail, Loki, Grafana) configurado
- ✅ Coleta básica de logs de containers Docker
- ✅ Datasource Loki configurado no Grafana

#### **Fase 2: Monitoramento de Infraestrutura**
- ✅ **Dashboard Infrastructure Health** implementado
- ✅ **Stack Prometheus** integrado (Prometheus + Node Exporter + cAdvisor)
- ✅ **4 Painéis de Sistema**:
  - CPU Usage (uso de processador)
  - Memory Usage (uso de memória RAM)
  - Network Traffic (tráfego de rede)
  - Disk Space Usage (uso de disco)
- ✅ Coleta de métricas reais de sistema via node_exporter
- ✅ Datasource Prometheus configurado no Grafana

### 🗑️ Funcionalidades Removidas (Por Solicitação)
- ❌ Dashboard Security & Fraud Detection
- ❌ Sistema de alertas baseado em Loki
- ❌ Logging estruturado customizado nos controllers PHP
- ❌ Parsing JSON no Promtail para eventos de negócio

## 🏗️ Arquitetura Atual

### **Stack de Monitoramento**
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Node Exporter │────│   Prometheus    │────│    Grafana      │
│  (métricas OS)  │    │ (coleta/storage)│    │ (visualização)  │
└─────────────────┘    └─────────────────┘    └─────────────────┘
┌─────────────────┐    ┌─────────────────┐           │
│     cAdvisor    │────│   Prometheus    │           │
│ (métricas cont.)│    │                 │           │
└─────────────────┘    └─────────────────┘           │
┌─────────────────┐    ┌─────────────────┐           │
│    Promtail     │────│      Loki       │───────────┘
│ (coleta logs)   │    │ (storage logs)  │
└─────────────────┘    └─────────────────┘
```

### **Serviços Docker**
- **web**: Aplicação PHP + Apache
- **db**: MySQL 8.0
- **prometheus**: Coleta e armazenamento de métricas
- **node_exporter**: Métricas do sistema operacional
- **cadvisor**: Métricas de containers Docker
- **loki**: Armazenamento de logs
- **promtail**: Coleta de logs
- **grafana**: Interface de visualização

## 📈 Dashboard Implementado

### **Infrastructure Health**
**Arquivo**: `dashboard-infrastructure-health.json`

**Painéis**:
1. **CPU Usage** - Uso de processador em porcentagem
2. **Memory Usage** - Uso de RAM em bytes e porcentagem  
3. **Network Traffic** - Tráfego de rede por interface
4. **Disk Space Usage** - Uso de disco por sistema de arquivos

**Métricas Prometheus**:
```promql
# CPU Usage
100 - (avg(irate(node_cpu_seconds_total{mode="idle"}[5m])) * 100)

# Memory Usage
node_memory_MemTotal_bytes - node_memory_MemAvailable_bytes

# Network Traffic
rate(node_network_transmit_bytes_total{device!="lo"}[5m])
rate(node_network_receive_bytes_total{device!="lo"}[5m])

# Disk Usage
node_filesystem_size_bytes{fstype!="tmpfs"}
node_filesystem_avail_bytes{fstype!="tmpfs"}
```

## 🚀 Próximas Fases Planejadas

### **Fase 3: Monitoramento de Performance da Aplicação** 
**Status**: 🔄 Pendente

**Objetivos**:
- Dashboard de performance da aplicação PHP
- Métricas de tempo de resposta HTTP
- Monitoramento de sessões e usuários ativos
- Análise de endpoints mais utilizados

**Implementações Previstas**:
- Dashboard Application Performance Monitoring
- Métricas via Apache mod_status
- Instrumentação customizada no PHP
- Alertas baseados em performance

### **Fase 4: Business Intelligence & Analytics**
**Status**: 🔄 Pendente  

**Objetivos**:
- Métricas de negócio específicas da aplicação
- Análise de uso por evento acadêmico
- Dashboards executivos com KPIs
- Relatórios de utilização

### **Fase 5: Produção e Manutenção**
**Status**: 🔄 Pendente

**Objetivos**:
- Políticas de retenção de dados
- Backup e recuperação
- Documentação operacional
- Treinamento de equipe

## ⚙️ Configuração de Produção

### **Portas Utilizadas**
- **80**: Aplicação web (ip-validator)
- **3000**: Grafana
- **3100**: Loki
- **8080**: cAdvisor
- **9090**: Prometheus
- **9100**: Node Exporter (interno)

### **Volumes Docker**
- `db_data`: Dados do MySQL
- Configurações em `./monitoramento/`

### **Arquivos de Configuração**
- `docker-compose.yml`: Orquestração dos serviços
- `monitoramento/prometheus.yml`: Configuração do Prometheus
- `monitoramento/promtail-config.yml`: Configuração do Promtail
- `monitoramento/grafana/prometheus.yml`: Datasource Prometheus
- `monitoramento/grafana/loki.yml`: Datasource Loki

## 📊 Como Usar

### **1. Iniciar o Stack Completo**
```bash
docker-compose up -d
```

### **2. Acessar Interfaces**
- **Grafana**: http://localhost:3000
- **Prometheus**: http://localhost:9090
- **cAdvisor**: http://localhost:8080

### **3. Importar Dashboard**
1. Acesse Grafana em http://localhost:3000
2. Vá em **Dashboards** > **Import**
3. Importe o arquivo: `monitoramento/grafana/dashboard-infrastructure-health.json`

### **4. Verificar Métricas**
- No Prometheus: http://localhost:9090/targets
- Verificar se todos os targets estão UP:
  - node_exporter:9100
  - cadvisor:8080
  - prometheus:9090

## 🔧 Manutenção

### **Logs do Sistema**
```bash
# Verificar logs dos serviços
docker-compose logs grafana
docker-compose logs prometheus
docker-compose logs loki
docker-compose logs promtail
```

### **Backup de Configurações**
- Fazer backup regular do diretório `monitoramento/`
- Exportar dashboards do Grafana periodicamente

### **Atualizações**
- Atualizar imagens Docker conforme necessário
- Revisar configurações a cada atualização
- Testar dashboards após atualizações

## 📝 Próximos Passos

1. **Fase 3**: Implementar monitoramento de performance da aplicação
2. **Treinamento**: Capacitar equipe no uso do Grafana
3. **Documentação**: Criar guias operacionais
4. **Otimização**: Ajustar configurações baseado no uso real

---

## 📊 Resumo Executivo

**Estado Atual**: ✅ Infrastructure Health Dashboard funcional
**Próximo Objetivo**: 🎯 Application Performance Monitoring  
**Valor Entregue**: Visibilidade completa da infraestrutura do sistema
**ROI**: Detecção proativa de problemas de sistema e capacidade

---

**Implementação Atual**: Fase 2 concluída com sucesso
**Próxima Implementação**: Aguardando aprovação para Fase 3