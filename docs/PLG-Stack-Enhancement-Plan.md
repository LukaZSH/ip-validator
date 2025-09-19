# 🔍 Plano de Melhoria do Stack PLG (Promtail-Loki-Grafana)

Sistema de Validação de Presença UNESPAR - Apucarana

## 📊 Estado Atual vs. Melhorias Propostas

### 🟡 Configuração Atual (Básica)
- Coleta genérica de logs de containers Docker
- Datasource Loki básico no Grafana  
- Sem parsing específico para Apache/PHP/MySQL
- Sem métricas de performance ou business
- Sem alertas configurados

### 🟢 Logs Valiosos Identificados

#### 📍 Logs de Aplicação (Mais Críticos)
1. **Apache Access Logs** - Padrões de acesso, IPs suspeitos, performance
2. **PHP Error Logs** - Erros de aplicação, falhas de autenticação
3. **MySQL Slow Query Logs** - Queries lentas, performance DB
4. **Sistema de Presença** - Tentativas de fraude, registros por IP

#### 📍 Logs de Infraestrutura
1. **Container Health** - CPU, memória, restart counts
2. **Network Logs** - Conexões, latência
3. **Sistema de arquivos** - Espaço em disco

## 🚀 Melhorias Propostas

### 1. Enhanced Promtail Configuration

```yaml
# monitoramento/promtail-config-enhanced.yml
server:
  http_listen_port: 9080
  grpc_listen_port: 0

positions:
  filename: /tmp/positions.yaml

clients:
  - url: http://loki:3100/loki/api/v1/push

scrape_configs:
  # Apache Access Logs
  - job_name: apache_access
    static_configs:
      - targets: [localhost]
        labels:
          job: apache_access
          service: ip-validator
          environment: production
          __path__: /var/lib/docker/containers/ip-validator-web-*/*-json.log
    pipeline_stages:
      - docker: {}
      - match:
          selector: '{job="apache_access"}'
          stages:
          - regex:
              expression: '^(?P<remote_addr>\S+) (?P<remote_user>\S+) (?P<time_local>\[[^\]]+\]) "(?P<method>\S+) (?P<request_uri>\S+) (?P<server_protocol>\S+)" (?P<status>\d+) (?P<body_bytes_sent>\d+) "(?P<http_referer>[^"]*)" "(?P<http_user_agent>[^"]*)"'
          - labels:
              method:
              status:
              remote_addr:
          - timestamp:
              source: time_local
              format: '[02/Jan/2006:15:04:05 -0700]'

  # MySQL Logs (Slow Queries + Errors)
  - job_name: mysql_logs
    static_configs:
      - targets: [localhost]
        labels:
          job: mysql_logs
          service: ip-validator-db
          __path__: /var/lib/docker/containers/ip-validator-db-*/*-json.log
    pipeline_stages:
      - docker: {}
      - match:
          selector: '{job="mysql_logs"}'
          stages:
          - regex:
              expression: '(?P<timestamp>\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d+Z) (?P<level>\w+) (?P<message>.*)'
          - labels:
              level:

  # PHP Application Logs
  - job_name: php_app
    static_configs:
      - targets: [localhost]
        labels:
          job: php_app
          service: ip-validator
          __path__: /var/lib/docker/containers/ip-validator-web-*/*-json.log
    pipeline_stages:
      - docker: {}
      - match:
          selector: '{job="php_app"} |~ "PHP|ERROR|WARN"'
          stages:
          - regex:
              expression: '\[(?P<timestamp>[^\]]+)\] (?P<level>\w+): (?P<message>.*)'
          - labels:
              level:

  # Container Health Metrics
  - job_name: container_health
    static_configs:
      - targets: [localhost]
        labels:
          job: container_health
          __path__: /var/lib/docker/containers/*/*-json.log
    pipeline_stages:
      - docker: {}
      - match:
          selector: '{job="container_health"}'
          stages:
          - json:
              expressions:
                container_name: attrs.tag
          - regex:
              expression: '(?P<container_service>ip-validator|mysql|grafana|loki|promtail)'
              source: container_name
          - labels:
              container_service:
```

### 2. Dashboard Strategy (RED + Business Metrics)

#### 📈 Dashboard #1: "IP Validator - Application Overview"
**Objetivo**: Visão geral da saúde da aplicação

**Painéis**:
- **Request Rate**: Requests/min por endpoint (/login, /admin, /evento)
- **Error Rate**: 4xx/5xx errors, timeouts
- **Response Duration**: P50, P95, P99 latências
- **Active Events**: Número de eventos ativos
- **Unique Visitors**: IPs únicos por dia
- **Authentication**: Login success/failure rates

**Queries Loki**:
```logql
# Request Rate por endpoint
rate({job="apache_access"}[5m]) | json | line_format "{{.request_uri}}"

# Error Rate
sum(rate({job="apache_access", status=~"4..|5.."}[5m])) / sum(rate({job="apache_access"}[5m])) * 100

# Response Time (via logs Apache)
histogram_quantile(0.95, sum(rate({job="apache_access"}[5m])) by (le))
```

#### 📊 Dashboard #2: "Security & Fraud Detection"
**Objetivo**: Detecção de atividades suspeitas e tentativas de fraude

**Painéis**:
- **Suspicious IPs**: Multiple failed logins
- **Presence Fraud**: Multiple registrations mesmo IP
- **Geographic Analysis**: Origem dos acessos (se disponível)
- **Failed Authentications**: Tentativas de login falhadas
- **QR Code Generation**: Sucessos vs falhas
- **Brute Force Detection**: Tentativas consecutivas de login

**Queries Loki**:
```logql
# IPs com múltiplas falhas de login
count by (remote_addr) (rate({job="php_app"} |~ "login.*failed"[5m]))

# Registros de presença duplicados por IP
count by (remote_addr) (rate({job="php_app"} |~ "presence.*registered"[1h]))

# Falhas de autenticação
sum(rate({job="php_app"} |~ "authentication.*failed"[5m]))
```

#### 🔧 Dashboard #3: "Infrastructure Health"
**Objetivo**: Monitoramento da infraestrutura e recursos

**Painéis**:
- **Container Status**: UP/DOWN, restart counts
- **Resource Usage**: CPU, memória, disk por container
- **Database Performance**: Slow queries, conexões ativas
- **Network**: Latência, packet loss
- **Log Volume**: Logs/min por serviço

**Queries Loki**:
```logql
# Container restarts
count by (container_service) (rate({job="container_health"} |~ "restart"[1h]))

# MySQL slow queries
count(rate({job="mysql_logs"} |~ "slow query"[5m]))

# Log volume por serviço
sum by (job) (rate({job=~".+"}[1m]))
```

#### ⚠️ Dashboard #4: "Alerts & Incidents"
**Objetivo**: Visibilidade de alertas ativos e histórico de incidentes

**Painéis**:
- **Active Alerts**: Status atual dos alertas
- **Response Times**: SLA compliance
- **Error Trends**: Evolução de erros ao longo do tempo
- **System Uptime**: Disponibilidade dos serviços
- **Incident Timeline**: Linha do tempo de incidentes

### 3. Alerting Strategy

#### Alertas Críticos Propostos

1. **Application Down**
   ```yaml
   alert: ApplicationDown
   expr: up{job="ip-validator"} == 0
   for: 1m
   severity: critical
   ```

2. **High Error Rate**
   ```yaml
   alert: HighErrorRate
   expr: rate({job="apache_access", status=~"5.."}[5m]) / rate({job="apache_access"}[5m]) > 0.05
   for: 5m
   severity: warning
   ```

3. **Database Connection Issues**
   ```yaml
   alert: DatabaseConnectionIssues
   expr: rate({job="mysql_logs"} |~ "connection.*failed"[5m]) > 0
   for: 2m
   severity: critical
   ```

4. **Suspicious Login Activity**
   ```yaml
   alert: SuspiciousLoginActivity
   expr: rate({job="php_app"} |~ "login.*failed"[1m]) > 10
   for: 5m
   severity: warning
   ```

5. **Fraud Detection**
   ```yaml
   alert: PossiblePresenceFraud
   expr: count by (remote_addr) (rate({job="php_app"} |~ "presence.*registered"[1h])) > 1
   for: 0s
   severity: warning
   ```

6. **High Response Time**
   ```yaml
   alert: HighResponseTime
   expr: histogram_quantile(0.95, rate({job="apache_access"}[5m])) > 2
   for: 10m
   severity: warning
   ```

7. **Disk Space Low**
   ```yaml
   alert: DiskSpaceLow
   expr: rate({job="container_health"} |~ "disk.*usage.*[8-9][0-9]%"[5m]) > 0
   for: 5m
   severity: warning
   ```

### 4. Business Metrics Integration

#### Custom PHP Logging Enhancement

**Adicionar ao `src/Config/Database.php`**:
```php
private function logDatabaseMetrics($operation, $duration, $status) {
    error_log(json_encode([
        'event' => 'db_operation',
        'operation' => $operation,
        'duration_ms' => $duration,
        'status' => $status,
        'timestamp' => time(),
        'service' => 'ip-validator'
    ]));
}
```

**Adicionar ao `app/controllers/AuthController.php`**:
```php
private function logAuthenticationAttempt($username, $ip, $status) {
    error_log(json_encode([
        'event' => 'authentication_attempt',
        'username' => $username,
        'ip' => $ip,
        'status' => $status,
        'timestamp' => time(),
        'service' => 'ip-validator'
    ]));
}
```

**Adicionar ao `app/controllers/AdminController.php`** (QR Code):
```php
private function logQRCodeGeneration($eventSlug, $status, $duration = null) {
    error_log(json_encode([
        'event' => 'qr_code_generation',
        'event_slug' => $eventSlug,
        'status' => $status,
        'duration_ms' => $duration,
        'timestamp' => time(),
        'service' => 'ip-validator'
    ]));
}
```

### 5. Performance Monitoring

#### Métricas Essenciais
- **TTFB (Time to First Byte)**
- **Page Load Time**
- **Database Query Performance**
- **QR Code Generation Time**
- **Session Duration**
- **Bounce Rate por página**

#### Custom Metrics Collection
```php
// Performance Middleware para medir tempos de resposta
class PerformanceMiddleware {
    public function handle($request, $next) {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $duration = (microtime(true) - $startTime) * 1000;
        
        error_log(json_encode([
            'event' => 'request_performance',
            'endpoint' => $request->getUri(),
            'method' => $request->getMethod(),
            'status_code' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'timestamp' => time()
        ]));
        
        return $response;
    }
}
```

### 6. Implementation Roadmap

#### 🎯 Phase 1 (Imediato - 1 semana)
- [ ] Implementar enhanced Promtail config
- [ ] Criar Dashboard #1 (Application Overview)
- [ ] Configurar alertas críticos (app down, high errors)
- [ ] Testar parsing de logs Apache e MySQL

#### 🎯 Phase 2 (Curto Prazo - 2-3 semanas)
- [ ] Implementar Dashboard #2 (Security & Fraud Detection)
- [ ] Adicionar custom business metrics logging no PHP
- [ ] Configurar alertas de segurança
- [ ] Dashboard #3 (Infrastructure Health)

#### 🎯 Phase 3 (Médio Prazo - 1-2 meses)
- [ ] Dashboard #4 (Alerts & Incidents)
- [ ] Advanced correlations (logs ↔ metrics)
- [ ] Automated incident response
- [ ] Capacity planning dashboards
- [ ] Performance optimization baseada nos insights

#### 🎯 Phase 4 (Longo Prazo - 3+ meses)
- [ ] Machine Learning para detecção de anomalias
- [ ] Predictive alerting
- [ ] Advanced security analytics
- [ ] Integration com sistemas externos (UNESPAR)

### 7. Value Delivered

#### 🔍 Observabilidade Completa
- Visibilidade end-to-end da aplicação
- Detecção proativa de problemas
- Insights de segurança e fraude
- Correlação entre logs, métricas e eventos

#### ⚡ Performance
- Identificação de gargalos de performance
- Otimização de queries de banco de dados
- Monitoring de SLA e disponibilidade
- Análise de padrões de uso

#### 🛡️ Segurança
- Detecção automática de ataques
- Monitoramento de acessos suspeitos
- Compliance e auditoria
- Prevenção de fraudes no sistema de presença

#### 📊 Business Intelligence
- Análise de uso da aplicação por evento
- Padrões de acesso e comportamento dos usuários
- Métricas de adoção e engajamento
- Insights para melhorias na experiência do usuário

### 8. Maintenance and Best Practices

#### Log Retention Policy
- **Hot data**: 7 dias (acesso rápido)
- **Warm data**: 30 dias (acesso moderado)
- **Cold data**: 90 dias (arquivamento)

#### Dashboard Maintenance
- **Review mensal**: Verificar relevância dos painéis
- **Performance tuning**: Otimizar queries lentas
- **User feedback**: Coletar feedback dos usuários para melhorias

#### Alert Tuning
- **Baseline establishment**: Estabelecer baselines após 2 semanas
- **False positive reduction**: Ajustar thresholds baseado em dados reais
- **Escalation procedures**: Definir procedimentos de escalation

---

## 📝 Conclusão

Este plano transformará o stack PLG básico em uma plataforma completa de observabilidade, fornecendo insights valiosos tanto técnicos quanto de negócio para o Sistema de Validação de Presença da UNESPAR - Apucarana.

A implementação faseada permite uma adoção gradual e iterativa, começando com as funcionalidades mais críticas e evoluindo para recursos avançados conforme a maturidade do sistema de monitoramento.

---

**Próximos Passos**:
1. Revisar e aprovar o plano
2. Iniciar Phase 1 com enhanced Promtail configuration
3. Estabelecer baselines de performance
4. Treinar equipe nos novos dashboards e alertas

**Estimativa de Esforço**: 40-60 horas de desenvolvimento/configuração distribuídas ao longo de 2-3 meses.