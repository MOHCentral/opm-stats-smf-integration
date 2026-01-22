# SMF MOHAA Integration and Plugins

This document outlines the process for setting up the MOHAA integration and plugins within a Simple Machines Forum (SMF) installation.

## Directory Structure

This project contains two key directories for SMF integration:

- **`smf-mohaa/`**: This directory contains the core integration files necessary to prepare your SMF installation for the MOHAA-specific features. It includes database setup scripts, core file modifications, and custom themes. This is the foundation of the integration.

- **`smf-plugins/`**: This directory contains individual plugins that provide specific features for your MOHAA-integrated SMF forum. Each subdirectory within `smf-plugins/` represents a separate plugin that can be installed.

These two directories have distinct purposes and should be handled as separate components during the installation process.

## Installation Steps

### **Step 0: Identify your SMF Installation Directory**

Before you begin, you need to know where your SMF forum is installed on your server. This is the directory that contains files like `index.php`, `Settings.php`, `Sources/`, and `Themes/`.

If you are unsure, you can find this directory by looking at your Apache or Nginx configuration files for the `DocumentRoot` directive associated with your SMF site. For example, the path might be `/var/www/smf`.

All the following commands assume your SMF installation is located at `/var/www/smf`. **Remember to replace this path with your actual SMF installation path.**

### **Step 1: Back Up Your SMF Installation**

Before making any changes, it's **CRUCIAL** to create a backup of your entire SMF installation directory and your SMF database.

```bash
# Backup SMF files (replace /var/www/smf with your actual path)
sudo tar -czvf smf_backup_files_$(date +%Y%m%d).tar.gz /var/www/smf

# Backup SMF database (replace with your actual database name, username)
# You will be prompted for your database password.
sudo mysqldump -u your_db_user -p your_db_name > smf_backup_db_$(date +%Y%m%d).sql
```

### **Step 2: Core MOHAA Integration (`smf-mohaa`)**

This step merges the foundational MOHAA files into your SMF installation.

1.  **Copy Files**:
    From your project root, copy the contents of `smf-mohaa` into your SMF installation.

    ```bash
    # Copy SQL installation scripts
    sudo cp smf-mohaa/*.sql /var/www/smf/

    # Copy PHP integration files
    sudo cp smf-mohaa/*.php /var/www/smf/

    # Merge Sources directory
    sudo cp -R smf-mohaa/Sources/* /var/www/smf/Sources/

    # Merge Themes directory
    sudo cp -R smf-mohaa/Themes/* /var/www/smf/Themes/
    ```
    **Warning**: This will overwrite files in your SMF `Sources/` and `Themes/` directories if there are name conflicts. Ensure you have backups!

2.  **Execute SQL Scripts**:
    Run the SQL files that were copied from `smf-mohaa` to set up database tables for the MOHAA integration.

    ```bash
    # Navigate to your SMF installation directory
    cd /var/www/smf

    # Example for executing scripts (replace with your DB user and name)
    sudo mysql -u your_db_user -p your_db_name < install_achievements.sql
    sudo mysql -u your_db_user -p your_db_name < install_tournaments.sql
    sudo mysql -u your_db_user -p your_db_name < link_identity.sql
    ```

3.  **Run PHP Integration Scripts**:
    Execute any special PHP scripts from `smf-mohaa` that perform integration tasks.

    ```bash
    # Example (run from your SMF directory)
    sudo php /var/www/smf/register_hooks.php
    sudo php /var/www/smf/setup_tournament_db.php
    ```

### **Step 3: Install SMF MOHAA Plugins (`smf-plugins`)**

This step installs the individual feature plugins.

For each plugin subdirectory inside your project's `smf-plugins/` (e.g., `mohaa_achievements`, `mohaa_players`, etc.):

1.  **Copy Plugin Files**:
    Copy the plugin's `Sources/`, `Themes/`, or any other directory contents into the corresponding locations within your `/var/www/smf` directory.

    ```bash
    # Example for mohaa_achievements plugin (from your project root):
    sudo cp -R smf-plugins/mohaa_achievements/Sources/* /var/www/smf/Sources/
    sudo cp -R smf-plugins/mohaa_achievements/Themes/* /var/www/smf/Themes/
    
    # Repeat for other plugins like mohaa_players, mohaa_servers, etc.
    ```
    **Warning**: Again, this will overwrite files if there are name conflicts.

2.  **Activate & Configure in SMF Admin Panel**:
    Log in to your SMF forum as an administrator.
    *   Navigate to **Admin Panel > Package Manager**. You might see new packages or options related to the MOHAA plugins. Install or enable them as required.
    *   Look for any new configuration sections in **Admin Panel > Configuration** or **Admin Panel > Features and Options** to set up the newly installed plugins.

By following this process, you will have a fully integrated and feature-rich MOHAA statistics and community platform within your SMF forum.