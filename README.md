# BuildPRO 

## Installation Guide

1. **Duplicate Environment File:**
   - Duplicate the provided `.env.local` file and rename the duplicate to `.env`.

2. **Edit Environment Configuration:**
   - Open the `.env` file and modify it according to your project's specific configuration.

3. **Set Host Information:**
   - Define the `APP_URL` in the `.env` file based on your project's host. Refer to the example in `.env.example`.

4. **Install Dependencies:**
   - Run the command `composer install` to install the necessary dependencies.

5. **Configure Database:**
   - Adjust the database settings in the `.env` file to match your database configuration.

6. **Run Database Migrations:**
   - Execute `php artisan migrate` to apply the required database migrations.

7. **Seed Database:**
   - Populate the database with initial data by running `php artisan db:seed`.

8. **Generate Application Key:**
   - Run `php artisan key:generate` to generate a unique application key.

9. **Create Storage Symbolic Link:**
   - Run `php artisan storage:link` to establish a symbolic link between the public folder and the storage folder.

10. **Optimize and Clear Cache:**
    - Execute `php artisan optimize:clear` to clear the application cache.

## Access Credentials

The project can be accessed using the following credentials:


## Overview

This starter template streamlines the setup process for a multi-tenancy Laravel project. The installation steps ensure proper configuration and data seeding. Here's a detailed breakdown of the key steps:

1. **Duplicate and Edit `.env`:**
   - Duplicate the provided `.env.local` file and adjust it to suit your project's needs.

2. **Set `APP_URL`:**
   - Configure the `APP_URL`  in the `.env` file based on your project's host information.

3. **Run `composer install`:**
   - Install project dependencies using the command `composer install`.

4. **Configure and Migrate Database:**
   - Adjust the database settings in the `.env` file.
   - Execute `php artisan migrate` to apply the required database migrations.

5. **Seed Database:**
   - Populate the database with initial data using `php artisan db:seed`.

6. **Generate Key and Create Storage Link:**
   - Generate an application key with `php artisan key:generate`.
   - Create a symbolic link from the public folder to the storage folder with `php artisan storage:link`.

7. **Optimize and Clear Cache:**
   - Clear the application cache using `php artisan optimize:clear`.

