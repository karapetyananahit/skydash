# Skydash

1. composer install
2. npm install
3. npm run dev
4. php artisan serve
5. .env
    MAIL_MAILER=smtp
    MAIL_HOST=smtp.mail.ru
    MAIL_PORT=465
    MAIL_USERNAME=anahit_karapetyan_01@mail.ru
    MAIL_PASSWORD=Bch6mW5crtz3F4AQyQwe
    MAIL_ENCRYPTION=ssl
    MAIL_FROM_ADDRESS="anahit_karapetyan_01@mail.ru"
    MAIL_FROM_NAME="${APP_NAME}"

    FACEBOOK_CLIENT_ID=1157458559414174
    FACEBOOK_CLIENT_SECRET=f586f1f7bc7213b2fab8131240724f96
    FACEBOOK_REDIRECT_URI=http://localhost:8000/auth/facebook/callback

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=skydash
    DB_USERNAME=root
    DB_PASSWORD=

6. php artisan migrate
