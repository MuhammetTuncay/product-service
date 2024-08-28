# Nginx Dockerfile
FROM nginx:latest

# Nginx yapılandırma dosyasını container içine kopyalayın
COPY configs/nginx/default-prod.conf /etc/nginx/conf.d/default.conf

# Uygulama dosyalarını container içine kopyalayın
COPY source /var/www/backend

# Nginx'in çalışmasını sağlamak için health check ekleyin
HEALTHCHECK CMD curl --fail http://localhost/healthz || exit 1
