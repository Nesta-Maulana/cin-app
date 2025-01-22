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






# Application Deployment Guide for AWS EC2 (BuilderApp)

This guide provides step-by-step instructions to set up the application on an AWS EC2 instance.

## Prerequisites
- Access to the AWS EC2 instance **BuilderApp**.
- Key pair file (`Builderapp.pem`) for SSH access.
- Git installed on the EC2 instance.
- Database credentials:
  - **Username**: `cin_app_user`
  - **Password**: `cinapp165`
  - **Database**: `cin_app`.

---

## Step 1: Access the EC2 Instance
Use the following SSH command to connect to the EC2 instance:

```bash
ssh -i "Builderapp.pem" -o IdentitiesOnly=yes ubuntu@ec2-52-77-233-114.ap-southeast-1.compute.amazonaws.com
```

---

## Step 2: Update System and Install Git
After logging in, update the system and install Git:

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install git -y
```

---

## Step 3: Clone or Update the Repository
Navigate to the application directory and either clone or update the repository.

If the repository has not been cloned yet:

```bash
cd /var/www/html
sudo git clone <repository_url> cinapp
```

If the repository already exists, update it using:

```bash
cd /var/www/html/cinapp
sudo git pull
```

---

## Step 4: Set File Permissions
Ensure the application folder has the correct permissions:

```bash
sudo chown -R www-data:www-data /var/www/html/cinapp
sudo chmod -R 755 /var/www/html/cinapp
```

---

## Step 5: Configure the Database
Edit the application configuration file to set the database credentials.

If using a `.env` file:

```bash
sudo nano /var/www/html/cinapp/.env
```

Add or update the following lines:

```
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=cin_app
DB_USERNAME=cin_app_user
DB_PASSWORD=cinapp165
```

Save changes and exit the editor.

---

## Step 6: Restart the Web Server
Restart the Apache server to apply changes:

```bash
sudo systemctl restart apache2
```

---

## Step 7: Verify the Application
Access the application via a web browser using the public IP or domain of the EC2 instance:

```
http://<public_ip_or_domain>/cinapp
```

Replace `<public_ip_or_domain>` with the actual public IP or domain.

---

## Troubleshooting
- **Logs**: Check Apache logs for errors if the application fails to load:
  ```bash
  sudo tail -f /var/log/apache2/error.log
  ```
- **Firewall**: Ensure the EC2 Security Group allows traffic on ports 80 (HTTP) and 443 (HTTPS).

---

This documentation ensures a smooth setup of your application on the **BuilderApp** EC2 instance. For further assistance, feel free to reach out.
