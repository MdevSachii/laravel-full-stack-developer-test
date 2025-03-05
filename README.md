# Laravel Full Stack Developer

Copy the example .env file:
    - cp .env.example .env

Update the .env file with your database details:

DB_DATABASE=your_database_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

Run the following command to set up and start the project: in src

make up

Once the containers are up, access the shell:

make shell


Install Dependencies
Inside the shell, run:

composer install

Generate the application key:

php artisan key:generate


Install npm dependencies:

npm install


Run database migrations:
php artisan migrate

Run the Application
npm run dev