The frontend of the Sanctuary project is in React and the backend is in Symfony.


# Installation of Symfony

## Step 1: Clone the repository

Clone the repository in your local Projects folder

```
git@github.com:coeus-solutions/sanctuary-backend.git
```


## Step 2: Install Composer

Go to the sanctuary-backend directory from the directory of sanctuary-web and run command

```
cd sanctuary-backend
composer install
```

## Step 3: Create the database and data required to run system properly

This will generate initial database schema

```
bin/console doctrine:database:create
bin/console doctrine:migrations:migrate
```

## Step 4: Update .env file