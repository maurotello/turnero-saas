## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.



##  Datos Webhook de Mercado Pago
- Token: `APP_USR-5698414115490232-112307-3021359f518c577ba134fc078a706580-3007590783`
- Webhook URL local: `http://127.0.0.1:8000/payments/mp/webhook`
- Webhook URL para producción: `MERCADOPAGO_WEBHOOK_URL=https://tu-dominio.onrender.com/booking/TU-SLUG-DE-EMPRESA/mp-webhook`

##  Datos Certificado TiDB
- Usuario: `ufWmG4DGVxy7ykc.root`
- Contraseña: `BQQozwXVs15bjHeT`

## Key generated
key: base64:NLKgscQkNuJzl9hdqllah4Si55tLfx/j3cJgzlqrE28=

## Variables de entorno para render

APP_NAME=Turnero SaaS
APP_ENV=production
APP_KEY=base64:EL_QUE_GENERASTE_CON_KEY:GENERATE
APP_DEBUG=false
APP_URL=https://turnero-saas.onrender.com

DB_CONNECTION=mysql
DB_HOST=gateway01.us-east-1.prod.aws.tidbcloud.com
DB_PORT=4000
DB_DATABASE=turnero_saas
DB_USERNAME=tu_usuario_tidb
DB_PASSWORD=tu_password_tidb
MYSQL_ATTR_SSL_CA=/var/www/html/storage/certs/tidb-ca.pem

QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database

LOG_CHANNEL=stderr

WHATSAPP_VERIFY_TOKEN=el_mismo_que_tenés_en_local
WHATSAPP_APP_SECRET=el_de_meta

