#!/usr/bin/env sh
set -e

# Mensagem util no log
echo "[entrypoint] Iniciando container Laravel..."

# Garantir diretorios de cache/sessoes/views existentes e permissoes corretas
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache || true
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

# Gerar APP_KEY caso não exista
if [ -z "${APP_KEY}" ]; then
  echo "[entrypoint] Gerando APP_KEY..."
  php artisan key:generate --force >/dev/null 2>&1 || true
fi

# Se for SQLite, garanta o arquivo do banco
if [ "${DB_CONNECTION}" = "sqlite" ]; then
  echo "[entrypoint] DB_CONNECTION=sqlite: garantindo arquivo database/database.sqlite"
  mkdir -p database
  touch database/database.sqlite
fi

# Limpezas (nao falham o container se der erro)
php artisan config:clear >/dev/null 2>&1 || true
php artisan cache:clear  >/dev/null 2>&1 || true
php artisan route:clear  >/dev/null 2>&1 || true
php artisan view:clear   >/dev/null 2>&1 || true
php artisan permission:cache-reset >/dev/null 2>&1 || true

# Link de storage (idempotente)
php artisan storage:link >/dev/null 2>&1 || true

# Rodar migracoes automaticamente; se falhar (ex: sem DB), apenas loga e continua
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  echo "[entrypoint] Executando migrations..."
  php artisan migrate --force || echo "[entrypoint] Migrations falharam (provavel DB indisponivel). Continuando..."
fi

# Rodar seeders opcionalmente
if [ "${RUN_SEEDS:-false}" = "true" ]; then
  echo "[entrypoint] Executando seeders..."
  if [ -n "${SEED_CLASSES:-}" ]; then
    # Permite lista separada por vírgula: SEED_CLASSES="PlatformRolesAndPermissionsSeeder,DockerSuperAdminSeeder,OrganizationDataSeeder"
    IFS=',' read -r -a SEEDS <<< "${SEED_CLASSES}"
    for s in "${SEEDS[@]}"; do
      s_trim=$(echo "$s" | xargs)
      if [ -n "$s_trim" ]; then
        echo "[entrypoint] Seeding class: $s_trim"
        php artisan db:seed --class="$s_trim" --force || echo "[entrypoint] Seeder $s_trim falhou. Continuando..."
      fi
    done
  else
    php artisan db:seed --force || echo "[entrypoint] db:seed falhou. Continuando..."
  fi
fi

# Otimizacoes
php artisan optimize || true

# Iniciar servidor HTTP ouvindo na porta dinamica do Railway ($PORT)
PORT_TO_USE=${PORT:-8080}
echo "[entrypoint] Servidor ouvindo em 0.0.0.0:${PORT_TO_USE}"
exec php artisan serve --host=0.0.0.0 --port=${PORT_TO_USE}
